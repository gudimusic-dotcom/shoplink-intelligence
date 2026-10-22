<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use SimpleXMLElement;

class HurtelStockImportService
{
    public function parse(UploadedFile $file): array
    {
        libxml_use_internal_errors(true);
        $xml = simplexml_load_file($file->getRealPath());

        if (! $xml instanceof SimpleXMLElement || $xml->getName() !== 'offer') {
            throw new \RuntimeException('XML feed nemá očakávaný formát IdoSell offer.');
        }

        $rows = [];
        foreach ($xml->products->product ?? [] as $product) {
            $supplierCode = trim((string) ($product['id'] ?? ''));
            if ($supplierCode === '') {
                continue;
            }

            $quantity = 0;
            foreach ($product->sizes->size ?? [] as $size) {
                foreach ($size->stock ?? [] as $stock) {
                    $quantity += max(0, (int) ($stock['quantity'] ?? 0));
                }
            }

            $rows[$supplierCode] = [
                'supplier_name' => 'Hurtel',
                'supplier_code' => $supplierCode,
                'stock_status' => $quantity > 0 ? 'in_stock' : 'out_of_stock',
            ];
        }

        return array_values($rows);
    }
}
