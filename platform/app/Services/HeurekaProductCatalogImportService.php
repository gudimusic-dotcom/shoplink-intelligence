<?php

namespace App\Services;

use App\Models\ImportJob;
use App\Models\ProductCatalogItem;
use Illuminate\Http\UploadedFile;
use XMLReader;
use Throwable;

class HeurekaProductCatalogImportService
{
    public function import(UploadedFile $file, int $userId): ImportJob
    {
        return $this->importPath($file->getRealPath(), $userId, $file->getClientOriginalName());
    }

    public function importPath(string $path, int $userId, string $fileName): ImportJob
    {
        $job = ImportJob::query()->create([
            'import_type' => 'product_catalog',
            'source_system' => 'google_merchant_xml',
            'file_name' => $fileName,
            'status' => 'running',
            'started_at' => now(),
            'uploaded_by' => $userId,
        ]);

        try {
            [$imported, $skipped] = $this->readItems($path);

            $job->update([
                'status' => 'completed',
                'finished_at' => now(),
                'total_rows' => $imported + $skipped,
                'imported_rows' => $imported,
                'skipped_rows' => $skipped,
            ]);
        } catch (Throwable $exception) {
            $job->update([
                'status' => 'failed',
                'finished_at' => now(),
                'error_count' => 1,
            ]);

            report($exception);
        }

        return $job->refresh();
    }

    /**
     * @return array{0:int,1:int}
     */
    private function readItems(string $path): array
    {
        $reader = new XMLReader();
        $reader->open($path);

        $imported = 0;
        $skipped = 0;
        $batch = [];

        while ($reader->read()) {
            if ($reader->nodeType !== XMLReader::ELEMENT || ! in_array($reader->localName, ['SHOPITEM', 'entry'], true)) {
                continue;
            }

            $item = $reader->localName === 'entry'
                ? $this->readGoogleMerchantEntry($reader)
                : $this->readShopItem($reader);

            if (($item['item_id'] ?? '') === '' || ($item['product_name'] ?? '') === '') {
                $skipped++;
                continue;
            }

            $batch[] = [
                'item_id' => $item['item_id'],
                'product_name' => $item['product_name'],
                'product_type' => $this->cleanProductType($item['product_type']) ?: null,
                'image_url' => $item['image_url'] ?: null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (count($batch) >= 500) {
                $this->upsertBatch($batch);
                $imported += count($batch);
                $batch = [];
            }
        }

        if ($batch !== []) {
            $this->upsertBatch($batch);
            $imported += count($batch);
        }

        $reader->close();

        return [$imported, $skipped];
    }


    /**
     * @return array{item_id:string,product_name:string,product_type:string,image_url:string}
     */
    private function readGoogleMerchantEntry(XMLReader $reader): array
    {
        $item = [
            'item_id' => '',
            'product_name' => '',
            'product_type' => '',
            'image_url' => '',
        ];
        $depth = $reader->depth;

        while ($reader->read()) {
            if ($reader->nodeType === XMLReader::END_ELEMENT && $reader->localName === 'entry' && $reader->depth === $depth) {
                break;
            }

            if ($reader->nodeType !== XMLReader::ELEMENT) {
                continue;
            }

            if ($reader->localName === 'id') {
                $item['item_id'] = trim((string) $reader->readString());
                continue;
            }

            if ($reader->localName === 'title') {
                $item['product_name'] = trim((string) $reader->readString());
                continue;
            }

            if ($reader->localName === 'product_type') {
                $item['product_type'] = trim((string) $reader->readString());
                continue;
            }

            if ($reader->localName === 'image_link') {
                $item['image_url'] = trim((string) $reader->readString());
            }
        }

        return $item;
    }

    /**
     * @return array{item_id:string,product_name:string,product_type:string,image_url:string}
     */
    private function readShopItem(XMLReader $reader): array
    {
        $item = [
            'item_id' => '',
            'product_name' => '',
            'product_type' => '',
            'image_url' => '',
        ];
        $depth = $reader->depth;

        while ($reader->read()) {
            if ($reader->nodeType === XMLReader::END_ELEMENT && $reader->name === 'SHOPITEM' && $reader->depth === $depth) {
                break;
            }

            if ($reader->nodeType !== XMLReader::ELEMENT) {
                continue;
            }

            if ($reader->name === 'ITEM_ID') {
                $item['item_id'] = trim((string) $reader->readString());
                continue;
            }

            if ($reader->name === 'PRODUCTNAME') {
                $item['product_name'] = trim((string) $reader->readString());
                continue;
            }

            if ($reader->name === 'PRODUCT') {
                $item['product_type'] = trim((string) $reader->readString());
                continue;
            }

            if ($reader->name === 'IMGURL') {
                $item['image_url'] = trim((string) $reader->readString());
            }
        }

        return $item;
    }

    /**
     * @param array<int, array<string, mixed>> $batch
     */
    private function upsertBatch(array $batch): void
    {
        ProductCatalogItem::query()->upsert(
            $batch,
            ['item_id'],
            ['product_name', 'product_type', 'image_url', 'updated_at'],
        );
    }

    private function cleanProductType(string $productType): string
    {
        return trim((string) preg_replace('/^\s*ESHOP\s*>\s*/iu', '', $productType));
    }
}
