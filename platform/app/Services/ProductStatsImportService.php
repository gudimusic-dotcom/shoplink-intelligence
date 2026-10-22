<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use RuntimeException;
use SimpleXMLElement;
use ZipArchive;

class ProductStatsImportService
{
    public function parse(UploadedFile $file): array
    {
        $zip = new ZipArchive();
        $opened = $zip->open($file->getRealPath());

        if ($opened !== true) {
            throw new RuntimeException('XLSX súbor sa nepodarilo otvoriť.');
        }

        $workbookXml = $zip->getFromName('xl/workbook.xml');
        $relationsXml = $zip->getFromName('xl/_rels/workbook.xml.rels');
        $sharedStringsXml = $zip->getFromName('xl/sharedStrings.xml');

        if ($workbookXml === false || $relationsXml === false) {
            $zip->close();
            throw new RuntimeException('XLSX súbor nemá čitateľnú workbook štruktúru.');
        }

        $sharedStrings = $sharedStringsXml !== false ? $this->parseSharedStrings($sharedStringsXml) : [];
        [$sheetName, $sheetPath] = $this->resolveFirstSheet($workbookXml, $relationsXml);
        $sheetXml = $zip->getFromName($sheetPath);

        if ($sheetXml === false) {
            $zip->close();
            throw new RuntimeException('XLSX súbor nemá čitateľný prvý sheet.');
        }

        $rows = $this->parseSheetRows($sheetXml, $sharedStrings);
        $zip->close();

        $header = $this->normalizeHeaderRow($rows[0] ?? []);
        $dataRows = array_slice($rows, 1);

        return [
            'sheet_name' => $sheetName,
            'rows' => array_values(array_filter(
                array_map(fn (array $row) => $this->mapRow($row, $header), $dataRows),
                fn (?array $row) => $row !== null
            )),
        ];
    }

    private function resolveFirstSheet(string $workbookXml, string $relationsXml): array
    {
        $workbook = new SimpleXMLElement($workbookXml);
        $workbook->registerXPathNamespace('main', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
        $workbook->registerXPathNamespace('r', 'http://schemas.openxmlformats.org/officeDocument/2006/relationships');
        $sheet = $workbook->xpath('//main:sheets/main:sheet[1]')[0] ?? null;

        if (! $sheet) {
            throw new RuntimeException('Workbook neobsahuje žiadny sheet.');
        }

        $sheetName = (string) $sheet['name'];
        $relationId = (string) $sheet->attributes('http://schemas.openxmlformats.org/officeDocument/2006/relationships')['id'];

        $relations = new SimpleXMLElement($relationsXml);
        $target = null;
        foreach ($relations->Relationship as $relation) {
            if ((string) $relation['Id'] === $relationId) {
                $target = 'xl/'.ltrim((string) $relation['Target'], '/');
                break;
            }
        }

        if (! $target) {
            throw new RuntimeException('Nepodarilo sa nájsť sheet target.');
        }

        return [$sheetName, $target];
    }

    private function parseSharedStrings(string $xml): array
    {
        $root = new SimpleXMLElement($xml);
        $root->registerXPathNamespace('main', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
        $strings = [];

        foreach ($root->xpath('//main:si') as $item) {
            $item->registerXPathNamespace('main', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
            $parts = [];
            foreach ($item->xpath('.//main:t') as $textNode) {
                $parts[] = (string) $textNode;
            }
            $strings[] = implode('', $parts);
        }

        return $strings;
    }

    private function parseSheetRows(string $xml, array $sharedStrings): array
    {
        $sheet = new SimpleXMLElement($xml);
        $sheet->registerXPathNamespace('main', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
        $parsed = [];

        foreach ($sheet->xpath('//main:sheetData/main:row') as $row) {
            $row->registerXPathNamespace('main', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
            $values = [];
            foreach ($row->xpath('./main:c') as $cell) {
                $cell->registerXPathNamespace('main', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
                $ref = (string) $cell['r'];
                $columnIndex = $this->columnLettersToIndex(preg_replace('/\d+/', '', $ref));
                $type = (string) $cell['t'];
                $valueNode = $cell->xpath('./main:v')[0] ?? null;
                $value = $valueNode ? (string) $valueNode : null;

                if ($type === 's' && $value !== null) {
                    $value = $sharedStrings[(int) $value] ?? null;
                }

                $values[$columnIndex] = $value;
            }

            if ($values === []) {
                continue;
            }

            $maxIndex = max(array_keys($values));
            $rowData = [];
            for ($i = 0; $i <= $maxIndex; $i++) {
                $rowData[] = $values[$i] ?? null;
            }
            $parsed[] = $rowData;
        }

        return $parsed;
    }

    private function mapRow(array $row, array $header): ?array
    {
        $sku = trim((string) $this->valueByAliases($row, $header, ['Kód']));
        $productName = trim((string) $this->valueByAliases($row, $header, ['Názov']));

        if ($sku === '' || $productName === '' || mb_strtolower($sku) === 'sku') {
            return null;
        }

        $supplierRaw = trim((string) $this->valueByAliases($row, $header, ['Dodávateľ', 'DOD']));

        return [
            'sku' => $sku,
            'ean' => $this->nullableString($this->valueByAliases($row, $header, ['EAN', 'Kod EAN'])),
            'stock_code' => $this->nullableString($this->valueByAliases($row, $header, ['Skladový kód'])),
            'supplier_raw' => $supplierRaw,
            'supplier_name' => $this->extractSupplierName($supplierRaw),
            'supplier_code' => $this->nullableString($this->valueByAliases($row, $header, ['Dod.kód.'])),
            'product_name' => $productName,
            'stock_s1' => $this->toInt($this->valueByAliases($row, $header, ['S1'])),
            'stock_s2' => $this->toInt($this->valueByAliases($row, $header, ['S2'])),
            'sales_period' => $this->toInt($this->valueByAliases($row, $header, ['Predaj', 'Pre'])),
            'market_noa' => $this->toInt($this->valueByAliases($row, $header, ['NoA'])),
            'market_alza' => $this->toInt($this->valueByAliases($row, $header, ['Alz'])),
            'market_pr1' => $this->toInt($this->valueByAliases($row, $header, ['Pr1'])),
            'market_pr2' => $this->toInt($this->valueByAliases($row, $header, ['Pr2'])),
            'sales_p30' => $this->toInt($this->valueByAliases($row, $header, ['P30'])),
            'sales_pop' => $this->toInt($this->valueByAliases($row, $header, ['POP'])),
            'external_stock' => $this->toInt($this->valueByAliases($row, $header, ['Ext'])),
            'clearance_flag' => $this->normalizeClearanceFlag($this->valueByAliases($row, $header, ['Vypr', 'Výpredaj', 'Vypredaj', 'vypredaj'])),
            'purchase_cost' => $this->toFloat($this->valueByAliases($row, $header, ['NC bez DPH (€)', 'NC bez DPH', 'NC'])),
            'last_order_at' => $this->toDate($this->valueByAliases($row, $header, ['LastOrder'])),
            'product_age_days' => $this->toInt($this->valueByAliases($row, $header, ['Dní'])),
            'raw_payload' => $row,
        ];
    }

    private function normalizeHeaderRow(array $row): array
    {
        return array_map(fn ($value) => trim((string) $value), $row);
    }

    private function valueByAliases(array $row, array $header, array $aliases): mixed
    {
        foreach ($aliases as $alias) {
            $index = array_search($alias, $header, true);
            if ($index !== false) {
                return $row[$index] ?? null;
            }
        }

        return null;
    }

    private function extractSupplierName(string $value): string
    {
        $value = trim($value);

        if ($value === '') {
            return 'Bez dodávateľa';
        }

        $segments = preg_split('/\s*,\s*/', $value, -1, PREG_SPLIT_NO_EMPTY);

        foreach ($segments as $segment) {
            preg_match_all('/[\p{L}\p{N}]+/u', (string) $segment, $matches);
            $parts = collect($matches[0] ?? [])
                ->filter(fn ($part) => preg_match('/\p{L}/u', $part) === 1)
                ->values()
                ->all();

            if ($parts !== []) {
                return mb_convert_case(mb_strtolower(implode(' ', $parts)), MB_CASE_TITLE, 'UTF-8');
            }
        }

        return 'Bez dodávateľa';
    }

    private function toDate(mixed $value): ?Carbon
    {
        if ($value === null || $value === '') {
            return null;
        }

        $stringValue = trim((string) $value);

        if ($stringValue === '' || ! preg_match('/\d/', $stringValue)) {
            return null;
        }

        if (is_numeric($value)) {
            return Carbon::create(1899, 12, 30)->addDays((int) round((float) $value));
        }

        return Carbon::parse($stringValue);
    }

    private function columnLettersToIndex(string $letters): int
    {
        $index = 0;
        foreach (str_split($letters) as $char) {
            $index = ($index * 26) + (ord($char) - 64);
        }

        return $index - 1;
    }

    private function nullableString(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return trim((string) $value);
    }

    private function toInt(mixed $value): int
    {
        return (int) round((float) ($value ?? 0));
    }

    private function toFloat(mixed $value): float
    {
        return round((float) ($value ?? 0), 2);
    }

    private function normalizeClearanceFlag(mixed $value): ?string
    {
        $string = trim((string) ($value ?? ''));

        if ($string === '' || $string === '0') {
            return null;
        }

        if ($string === '1') {
            return '1';
        }

        return $string;
    }
}
