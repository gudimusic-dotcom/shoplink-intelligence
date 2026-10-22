<?php

namespace App\Services;

use RuntimeException;
use SimpleXMLElement;
use ZipArchive;

class SimpleXlsxReader
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function rows(string $path): array
    {
        $zip = new ZipArchive();

        if ($zip->open($path) !== true) {
            throw new RuntimeException('XLSX súbor sa nepodarilo otvoriť.');
        }

        $sharedStrings = $this->sharedStrings($zip);
        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();

        if ($sheetXml === false) {
            throw new RuntimeException('XLSX súbor neobsahuje prvý hárok.');
        }

        $xml = new SimpleXMLElement($sheetXml);
        $rawRows = [];

        foreach ($xml->sheetData->row as $row) {
            $values = [];

            foreach ($row->c as $cell) {
                $reference = (string) $cell['r'];
                $columnIndex = $this->columnIndex($reference);
                $type = (string) $cell['t'];
                $value = isset($cell->v) ? (string) $cell->v : '';

                if ($type === 's') {
                    $value = $sharedStrings[(int) $value] ?? '';
                } elseif ($type === 'inlineStr') {
                    $value = (string) ($cell->is->t ?? '');
                }

                $values[$columnIndex] = $value;
            }

            if ($values !== []) {
                ksort($values);
                $rawRows[] = $values;
            }
        }

        if ($rawRows === []) {
            return [];
        }

        $headers = [];

        foreach ($rawRows[0] as $index => $value) {
            $headers[$index] = trim((string) $value);
        }

        $rows = [];

        foreach (array_slice($rawRows, 1) as $row) {
            $item = [];

            foreach ($headers as $index => $header) {
                if ($header === '') {
                    continue;
                }

                $item[$header] = $row[$index] ?? null;
            }

            if (array_filter($item, fn ($value) => $value !== null && $value !== '') !== []) {
                $rows[] = $item;
            }
        }

        return $rows;
    }

    /**
     * @return array<int, string>
     */
    private function sharedStrings(ZipArchive $zip): array
    {
        $xmlString = $zip->getFromName('xl/sharedStrings.xml');

        if ($xmlString === false) {
            return [];
        }

        $xml = new SimpleXMLElement($xmlString);
        $strings = [];

        foreach ($xml->si as $item) {
            if (isset($item->t)) {
                $strings[] = (string) $item->t;
                continue;
            }

            $text = '';

            foreach ($item->r as $run) {
                $text .= (string) $run->t;
            }

            $strings[] = $text;
        }

        return $strings;
    }

    private function columnIndex(string $reference): int
    {
        preg_match('/^[A-Z]+/', $reference, $matches);
        $letters = $matches[0] ?? 'A';
        $index = 0;

        foreach (str_split($letters) as $letter) {
            $index = ($index * 26) + (ord($letter) - 64);
        }

        return $index;
    }
}
