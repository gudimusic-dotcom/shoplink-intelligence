<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use RuntimeException;
use ZipArchive;

class FddStockImportService
{
    public function parse(UploadedFile $file): array
    {
        $contents = $this->csvContents($file);
        $handle = fopen('php://temp', 'r+');

        if ($handle === false) {
            throw new RuntimeException('CSV súbor sa nepodarilo pripraviť na čítanie.');
        }

        fwrite($handle, $contents);
        rewind($handle);
        $firstLine = fgets($handle);

        if ($firstLine === false) {
            fclose($handle);
            throw new RuntimeException('FDD skladový CSV súbor je prázdny.');
        }

        $delimiter = $this->detectDelimiter($firstLine);
        rewind($handle);
        $header = fgetcsv($handle, 0, $delimiter, '"', '\\');
        $headerMap = [];

        foreach ($header ?: [] as $index => $value) {
            $headerMap[$this->normalizeHeader((string) $value)] = $index;
        }

        $idIndex = $headerMap['id'] ?? null;
        $quantityIndex = $headerMap['ilosc produktow'] ?? null;

        if ($idIndex === null || $quantityIndex === null) {
            fclose($handle);
            throw new RuntimeException('FDD CSV musí obsahovať stĺpce id a ilość produktów.');
        }

        $rows = [];
        while (($data = fgetcsv($handle, 0, $delimiter, '"', '\\')) !== false) {
            $supplierCode = trim((string) ($data[$idIndex] ?? ''));

            if ($supplierCode === '') {
                continue;
            }

            $quantity = $this->parseQuantity((string) ($data[$quantityIndex] ?? '0'));
            $rows[$supplierCode] = [
                'supplier_name' => 'Fdd',
                'supplier_code' => $supplierCode,
                'stock_status' => $quantity > 0 ? 'in_stock' : 'out_of_stock',
                'stock_quantity' => $quantity,
            ];
        }

        fclose($handle);

        return array_values($rows);
    }

    private function csvContents(UploadedFile $file): string
    {
        if (strtolower($file->getClientOriginalExtension()) !== 'zip') {
            return (string) file_get_contents($file->getRealPath());
        }

        $zip = new ZipArchive();
        if ($zip->open($file->getRealPath()) !== true) {
            throw new RuntimeException('FDD ZIP súbor sa nepodarilo otvoriť.');
        }

        $csvName = null;
        for ($index = 0; $index < $zip->numFiles; $index++) {
            $name = (string) $zip->getNameIndex($index);
            if (str_ends_with(strtolower($name), '.csv') && ! str_starts_with($name, '__MACOSX/')) {
                $csvName = $name;
                break;
            }
        }

        $contents = $csvName !== null ? $zip->getFromName($csvName) : false;
        $zip->close();

        if ($contents === false) {
            throw new RuntimeException('FDD ZIP neobsahuje CSV súbor.');
        }

        return $contents;
    }

    private function detectDelimiter(string $line): string
    {
        return substr_count($line, ';') > substr_count($line, ',') ? ';' : ',';
    }

    private function normalizeHeader(string $header): string
    {
        $header = trim(mb_strtolower(preg_replace('/^\\xEF\\xBB\\xBF/', '', $header)));
        $header = strtr($header, [
            'ą' => 'a', 'ć' => 'c', 'ę' => 'e', 'ł' => 'l', 'ń' => 'n',
            'ó' => 'o', 'ś' => 's', 'ź' => 'z', 'ż' => 'z',
        ]);

        return trim((string) preg_replace('/[^a-z0-9]+/', ' ', $header));
    }

    private function parseQuantity(string $value): float
    {
        $value = str_replace(["\xc2\xa0", ' '], '', trim($value));
        $value = str_replace(',', '.', $value);

        return max(0, (float) $value);
    }
}
