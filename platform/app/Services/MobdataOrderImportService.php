<?php

namespace App\Services;

use App\Models\ExchangeRate;
use App\Models\ImportJob;
use App\Models\SalesChannel;
use App\Models\SalesOrder;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Throwable;

class MobdataOrderImportService
{
    public function __construct(private readonly SimpleXlsxReader $reader)
    {
    }

    public function import(UploadedFile $file, int $userId): ImportJob
    {
        $job = ImportJob::query()->create([
            'import_type' => 'sales_orders',
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
                foreach ($rows as $row) {
                    $status = trim((string) ($row['Status'] ?? ''));

                    if ($status !== 'Vybavené') {
                        $skipped++;
                        continue;
                    }

                    $orderCode = trim((string) ($row['Kód objednávky'] ?? ''));

                    if ($orderCode === '' || SalesOrder::query()->where('order_code', $orderCode)->exists()) {
                        $skipped++;
                        continue;
                    }

                    $currency = trim((string) ($row['Mena'] ?? ''));
                    $grossAmount = $this->decimal($row['MOC s DPH'] ?? 0);
                    $rate = $rates[$currency] ?? null;
                    $grossAmountEur = $this->amountToEur($grossAmount, $rate);

                    if ($grossAmountEur === null) {
                        $warnings++;
                    }

                    $ext = $this->blankToNull($row['Ext'] ?? null);
                    $portalCountry = $this->portalCountry($row['Portál'] ?? null);
                    $marketplaceDisplay = $this->marketplaceDisplay($ext, $portalCountry);
                    $channel = $this->resolveChannel($ext, $row['Portál'] ?? null);

                    SalesOrder::query()->create([
                        'import_job_id' => $job->id,
                        'sales_channel_id' => $channel?->id,
                        'ordered_at' => $this->date($row['Dátum'] ?? null),
                        'order_code' => $orderCode,
                        'ext' => $ext,
                        'marketplace_display' => $marketplaceDisplay,
                        'ext_id' => $this->blankToNull($row['Ext ID'] ?? null),
                        'portal' => $this->blankToNull($row['Portál'] ?? null),
                        'portal_country' => $portalCountry,
                        'product_models' => $this->blankToNull($row['Model'] ?? null),
                        'gross_amount' => $grossAmount,
                        'currency' => $currency,
                        'gross_amount_eur' => $grossAmountEur,
                        'channel_type' => $channel?->type ?? ($ext === null ? 'own_web' : 'unknown'),
                        'status' => $status,
                        'shipping_method' => $this->blankToNull($row['Doprava'] ?? null),
                        'shipping_amount' => $this->decimalOrNull($row['Cena dopravy'] ?? null),
                        'payment_method' => $this->blankToNull($row['Platba'] ?? null),
                        'payment_amount' => $this->decimalOrNull($row['Cena platby'] ?? null),
                        'invoice_number' => $this->blankToNull($row['Číslo faktúry'] ?? null),
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

    private function resolveChannel(?string $ext, mixed $portal): ?SalesChannel
    {
        if ($ext === null) {
            return SalesChannel::query()->firstOrCreate([
                'ext_code' => null,
            ], [
                'name' => 'MobileGo legacy web',
                'type' => 'own_web',
                'country_code' => $this->portalCountry($portal),
                'is_active' => true,
            ]);
        }

        return SalesChannel::query()->firstOrCreate([
            'ext_code' => $ext,
        ], [
            'name' => $ext,
            'type' => str_starts_with(strtolower($ext), 'mobilego') ? 'own_web' : 'marketplace',
            'country_code' => $this->portalCountry($portal),
            'is_active' => true,
        ]);
    }

    private function marketplaceDisplay(?string $ext, ?string $portalCountry): ?string
    {
        if ($ext === null) {
            return null;
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

    private function amountToEur(float $amount, mixed $rate): ?float
    {
        if ($rate === null || (float) $rate <= 0) {
            return null;
        }

        return round($amount / (float) $rate, 2);
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

        if ($value === '' || $value === '0') {
            return null;
        }

        return $value;
    }
}
