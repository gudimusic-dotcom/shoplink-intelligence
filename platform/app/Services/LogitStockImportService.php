<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use RuntimeException;
use SimpleXMLElement;

class LogitStockImportService
{
    public function parse(UploadedFile $file): array
    {
        libxml_use_internal_errors(true);
        $xml = simplexml_load_file($file->getRealPath());

        if (! $xml instanceof SimpleXMLElement || $xml->getName() !== 'products') {
            throw new RuntimeException('Logit XML feed nemá očakávaný formát products.');
        }

        $rows = [];
        foreach ($xml->product ?? [] as $product) {
            $status = mb_strtolower(trim((string) ($product->availability ?? '')));
            $stockStatus = $status === 'instock' ? 'in_stock' : 'out_of_stock';

            foreach ([(string) ($product->id ?? ''), (string) ($product->ean ?? '')] as $code) {
                $code = trim($code);

                if ($code === '') {
                    continue;
                }

                $rows[$code] = [
                    'supplier_name' => 'Logit',
                    'supplier_code' => $code,
                    'stock_status' => $stockStatus,
                ];
            }
        }

        return array_values($rows);
    }
}
