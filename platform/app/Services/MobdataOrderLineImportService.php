<?php

namespace App\Services;

use App\Models\ExchangeRate;
use App\Models\ImportJob;
use App\Models\SalesOrderLine;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Throwable;

class MobdataOrderLineImportService
{
    public function __construct(private readonly SimpleXlsxReader $reader)
    {
    }

    public function import(UploadedFile $file, int $userId): ImportJob
    {
        $job = ImportJob::query()->create([
            'import_type' => 'sales_order_lines',
            'source_system' => 'mobdata',
            'file_name' => $file->getClientOriginalName(),
            'status' => 'running',
            'started_at' => now(),
            'uploaded_by' => $userId,
        ]);

        try {
            $rows = $this->reader->rows($file->getRealPath());
            $rates = ExchangeRate::query()->pluck('rate_to_eur', 'currency');
            $imported = 0;
            $skipped = 0;
            $warnings = 0;

            DB::transaction(function () use ($rows, $rates, $job, &$imported, &$skipped, &$warnings): void {
                $period = $this->periodBounds($rows);

                if ($period !== null) {
                    SalesOrderLine::query()
                        ->whereDate('ordered_at', '>=', $period['from']->toDateString())
                        ->whereDate('ordered_at', '<=', $period['to']->toDateString())
                        ->delete();
                }

                foreach ($rows as $index => $row) {
                    $status = trim((string) ($row['Status'] ?? ''));
                    $orderCode = trim((string) ($row['Kód objednávky'] ?? ''));
                    $model = trim((string) ($row['Model'] ?? ''));

                    if ($orderCode === '' || $model === '') {
                        $skipped++;
                        continue;
                    }

                    $sourceHash = sha1(implode('|', [
                        $orderCode,
                        $model,
                        $this->decimal($row['Počet (ks)'] ?? 0),
                        $this->decimal($row['Cena bez DPH'] ?? 0),
                        $this->decimalOrNull($row['Cena nákupná'] ?? null) ?? '',
                        $this->decimalOrNull($row['Marža'] ?? null) ?? '',
                        $this->date($row['Dátum'] ?? null)?->toDateTimeString() ?? '',
                    ]));

                    if (SalesOrderLine::query()->where('source_hash', $sourceHash)->exists()) {
                        $skipped++;
                        continue;
                    }

                    $currency = trim((string) ($row['Mena'] ?? ''));
                    $rate = $rates[$currency] ?? null;
                    $netSalesAmount = $this->decimal($row['Cena bez DPH'] ?? 0);
                    $purchaseAmount = $this->decimalOrNull($row['Cena nákupná'] ?? null);
                    $marginAmount = $this->decimalOrNull($row['Marža'] ?? null);
                    $orderedAt = $this->date($row['Dátum'] ?? null);
                    $lineExt = $this->blankToNull($row['Ext'] ?? null);
                    $linePortalCountry = $this->portalCountry($row['Portál'] ?? null);
                    $portalCountry = $linePortalCountry;
                    $channelType = $lineExt === null ? 'own_web' : 'marketplace';
                    $marketplaceDisplay = $this->marketplaceDisplay($lineExt, $portalCountry);

                    if ($this->amountToEur($netSalesAmount, $rate) === null) {
                        $warnings++;
                    }

                    SalesOrderLine::query()->create([
                        'import_job_id' => $job->id,
                        'sales_order_id' => null,
                        'ordered_at' => $orderedAt,
                        'order_code' => $orderCode,
                        'portal' => $this->blankToNull($row['Portál'] ?? null),
                        'portal_country' => $portalCountry,
                        'brand' => $this->blankToNull($row['Výrobca'] ?? null),
                        'model' => $model,
                        'quantity' => $this->decimal($row['Počet (ks)'] ?? 0),
                        'category' => $this->blankToNull($row['Vetva'] ?? null),
                        'net_sales_amount' => $netSalesAmount,
                        'purchase_amount' => $purchaseAmount,
                        'margin_amount' => $marginAmount,
                        'currency' => $currency,
                        'net_sales_amount_eur' => $this->amountToEur($netSalesAmount, $rate),
                        'purchase_amount_eur' => $purchaseAmount !== null ? $this->amountToEur($purchaseAmount, $rate) : null,
                        'margin_amount_eur' => $marginAmount !== null ? $this->amountToEur($marginAmount, $rate) : null,
                        'channel_type' => $channelType,
                        'ext' => $lineExt,
                        'marketplace_display' => $marketplaceDisplay,
                        'status' => $status,
                        'source_hash' => $sourceHash,
                    ]);

                    $imported++;
                }
            });

            $job->update([
                'status' => 'completed',
                'finished_at' => now(),
                'total_rows' => count($rows),
                'imported_rows' => $imported,
                'skipped_rows' => $skipped,
                'warning_count' => $warnings,
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

    private function amountToEur(float $amount, mixed $rate): ?float
    {
        if ($rate === null || (float) $rate <= 0) {
            return null;
        }

        return round($amount / (float) $rate, 2);
    }

    private function date(mixed $value): ?Carbon
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            return Carbon::create(1899, 12, 30)->addSeconds((int) round((float) $value * 86400));
        }

        return Carbon::parse((string) $value);
    }

    private function periodBounds(array $rows): ?array
    {
        $dates = collect($rows)
            ->map(fn (array $row) => $this->date($row['Dátum'] ?? null))
            ->filter()
            ->sort()
            ->values();

        if ($dates->isEmpty()) {
            return null;
        }

        return [
            'from' => $dates->first()->copy()->startOfDay(),
            'to' => $dates->last()->copy()->endOfDay(),
        ];
    }

    private function decimal(mixed $value): float
    {
        if ($value === null || $value === '') {
            return 0.0;
        }

        return (float) str_replace(',', '.', (string) $value);
    }

    private function decimalOrNull(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        return $this->decimal($value);
    }

    private function blankToNull(mixed $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function marketplaceDisplay(?string $ext, ?string $portalCountry): ?string
    {
        if ($ext === null) {
            return $portalCountry ? 'Mobilego '.$portalCountry : 'Mobilego';
        }

        if (strtoupper($ext) === 'MALL') {
            return match ($portalCountry) {
                'SL' => 'MIMOVRSTE',
                'HR' => 'MALL.HR',
                default => 'MALL',
            };
        }

        return $ext;
    }

    private function portalCountry(mixed $portal): ?string
    {
        $portal = trim((string) $portal);

        if ($portal === '') {
            return null;
        }

        $parts = preg_split('/\s+/', $portal);

        return $parts !== false ? end($parts) ?: null : null;
    }
}
