<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use RuntimeException;

class ProductStatsIncomingImportService
{
    public function parseFddCsv(UploadedFile $file): array
    {
        $handle = fopen($file->getRealPath(), 'r');

        if ($handle === false) {
            throw new RuntimeException('CSV súbor sa nepodarilo otvoriť.');
        }

        $rows = [];
        $header = fgetcsv($handle, 0, ';');

        if ($header === false) {
            fclose($handle);
            throw new RuntimeException('CSV súbor je prázdny.');
        }

        while (($data = fgetcsv($handle, 0, ';')) !== false) {
            if ($data === [null] || $data === false) {
                continue;
            }

            $productId = trim((string) ($data[0] ?? ''));
            $ean = trim((string) ($data[2] ?? ''));
            $qty = (int) str_replace(',', '.', (string) ($data[3] ?? '0'));

            if ($ean === '' || $qty <= 0) {
                continue;
            }

            $rows[] = [
                'supplier_name' => 'Fdd',
                'supplier_product_id' => $productId !== '' ? $productId : null,
                'ean' => $ean,
                'qty_incoming' => $qty,
                'raw_payload' => [
                    'product_id' => $data[0] ?? null,
                    'product_name' => $data[1] ?? null,
                    'ean' => $data[2] ?? null,
                    'qty' => $data[3] ?? null,
                    'price_brutto' => $data[4] ?? null,
                    'price_netto' => $data[5] ?? null,
                ],
            ];
        }

        fclose($handle);

        return $rows;
    }
}
