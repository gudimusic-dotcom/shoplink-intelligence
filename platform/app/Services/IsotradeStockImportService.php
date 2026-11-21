<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use RuntimeException;
use SimpleXMLElement;

class IsotradeStockImportService
{
    public function parse(UploadedFile $file): array
    {
        libxml_use_internal_errors(true);
        $xml = simplexml_load_file($file->getRealPath());

        if (! $xml instanceof SimpleXMLElement || $xml->getName() !== 'offer') {
            throw new RuntimeException('Isotrade XML feed nemá očakávaný formát IOF offer.');
        }

        $rows = [];
        foreach ($xml->products->product ?? [] as $product) {
            foreach ($product->sizes->size ?? [] as $size) {
                $externalCode = trim((string) ($size->attributes('http://www.iai-shop.com/developers/iof/extensions.phtml')->code_external ?? ''));
                if ($externalCode === '') {
                    continue;
                }

                $quantity = 0;
                foreach ($size->stock ?? [] as $stock) {
                    $quantity += max(0, (int) ($stock['quantity'] ?? 0));
                }

                $rows[$externalCode] = [
                    'supplier_name' => 'Isotrade',
                    'supplier_code' => $externalCode,
                    'stock_status' => $quantity > 0 ? 'in_stock' : 'out_of_stock',
                    'stock_quantity' => $quantity,
                ];
            }
        }

        return array_values($rows);
    }
}
