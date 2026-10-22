<?php

namespace App\Jobs;

use App\Models\Monitor;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Symfony\Component\Process\Process;

class RunBrowserMonitorTestJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 1;

    public function __construct(
        public int $monitorId,
    ) {
        $this->onQueue('browser-monitors');
    }

    public function handle(): void
    {
        $monitor = Monitor::query()->find($this->monitorId);

        if (! $monitor) {
            return;
        }

        $monitor->update([
            'last_test_status' => 'running',
            'last_test_message' => 'Browser test práve beží vo workeri.',
        ]);

        $process = new Process([
            'node',
            base_path('scripts/browser-monitor-test.mjs'),
            $monitor->target_url,
        ], base_path(), [
            'HOME' => base_path(),
            'PLAYWRIGHT_BROWSERS_PATH' => base_path('.playwright-browsers'),
        ]);

        $process->setTimeout(90);
        $process->run();

        if (! $process->isSuccessful()) {
            $errorOutput = trim($process->getErrorOutput() ?: $process->getOutput());

            $monitor->update([
                'last_checked_at' => now(),
                'last_test_status' => 'failed',
                'last_test_message' => $errorOutput !== ''
                    ? mb_strimwidth(preg_replace('/\s+/', ' ', $errorOutput), 0, 460, '...')
                    : 'Browser worker sa nepodarilo dokončiť.',
                'last_test_challenge' => null,
            ]);

            return;
        }

        $payload = json_decode($process->getOutput(), true);

        if (! is_array($payload)) {
            $monitor->update([
                'last_checked_at' => now(),
                'last_test_status' => 'failed',
                'last_test_message' => 'Browser worker vrátil neplatný výsledok.',
                'last_test_challenge' => null,
            ]);

            return;
        }

        $priceCandidates = collect($payload['priceCandidates'] ?? [])
            ->filter(fn ($value) => is_string($value) && $value !== '')
            ->values();

        $firstPrice = $this->parseMoney($priceCandidates->first());

        $monitor->update([
            'last_checked_at' => now(),
            'last_test_status' => ($payload['challenge'] ?? null)
                ? 'blocked'
                : (($payload['ok'] ?? false) ? 'ok' : ($priceCandidates->isEmpty() ? 'partial' : 'failed')),
            'last_test_message' => $this->buildResultMessage($payload, $priceCandidates->all()),
            'last_test_challenge' => $payload['challenge'] ?? null,
            'last_value' => $firstPrice,
        ]);
    }

    private function buildResultMessage(array $payload, array $priceCandidates): string
    {
        if (! empty($payload['challenge'])) {
            return 'Browser narazil na challenge: '.$payload['challenge'].'.';
        }

        if ($priceCandidates !== []) {
            return 'Browser našiel ceny: '.implode(', ', array_slice($priceCandidates, 0, 3));
        }

        return $payload['message'] ?? 'Browser test skončil bez detailu.';
    }

    private function parseMoney(?string $value): ?float
    {
        if (! is_string($value) || $value === '') {
            return null;
        }

        $normalized = preg_replace('/[^\d,.\-]/', '', $value);

        if (! is_string($normalized) || $normalized === '') {
            return null;
        }

        $normalized = str_replace(' ', '', $normalized);

        if (str_contains($normalized, ',') && str_contains($normalized, '.')) {
            $normalized = str_replace('.', '', $normalized);
            $normalized = str_replace(',', '.', $normalized);
        } elseif (str_contains($normalized, ',')) {
            $normalized = str_replace(',', '.', $normalized);
        }

        return is_numeric($normalized) ? round((float) $normalized, 2) : null;
    }
}
