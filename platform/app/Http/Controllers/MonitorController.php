<?php

namespace App\Http\Controllers;

use App\Jobs\RunBrowserMonitorTestJob;
use App\Models\Monitor;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;

class MonitorController extends Controller
{
    public function index(): View
    {
        $typeOptions = [
            'price_watch' => 'Price watch',
        ];
        $engineOptions = [
            'simple' => 'Simple fetch',
            'browser' => 'Browser mode',
        ];

        $marketplaceOptions = [
            'Alza',
            'MALL',
            'MALL.HR',
            'Mimovrste',
            'Web',
            'Other',
        ];

        $monitors = Monitor::query()
            ->with('creator')
            ->latest()
            ->get();

        $stats = [
            [
                'label' => 'Aktívne',
                'value' => $monitors->where('is_active', true)->count(),
            ],
            [
                'label' => 'Pauza',
                'value' => $monitors->where('is_active', false)->count(),
            ],
            [
                'label' => 'Spustené alerty',
                'value' => $monitors->whereNotNull('last_triggered_at')->count(),
            ],
            [
                'label' => 'Queue',
                'value' => $monitors->whereIn('last_test_status', ['pending', 'running'])->count(),
            ],
        ];

        return view('monitors.index', [
            'monitors' => $monitors,
            'stats' => $stats,
            'typeOptions' => $typeOptions,
            'engineOptions' => $engineOptions,
            'marketplaceOptions' => $marketplaceOptions,
            'testResult' => session('monitor_test'),
            'selectedMonitor' => null,
        ]);
    }

    public function edit(Monitor $monitor): View
    {
        $typeOptions = [
            'price_watch' => 'Price watch',
        ];
        $engineOptions = [
            'simple' => 'Simple fetch',
            'browser' => 'Browser mode',
        ];

        $marketplaceOptions = [
            'Alza',
            'MALL',
            'MALL.HR',
            'Mimovrste',
            'Web',
            'Other',
        ];

        $monitors = Monitor::query()
            ->with('creator')
            ->latest()
            ->get();

        $stats = [
            [
                'label' => 'Aktívne',
                'value' => $monitors->where('is_active', true)->count(),
            ],
            [
                'label' => 'Pauza',
                'value' => $monitors->where('is_active', false)->count(),
            ],
            [
                'label' => 'Spustené alerty',
                'value' => $monitors->whereNotNull('last_triggered_at')->count(),
            ],
            [
                'label' => 'Queue',
                'value' => $monitors->whereIn('last_test_status', ['pending', 'running'])->count(),
            ],
        ];

        return view('monitors.index', [
            'monitors' => $monitors,
            'stats' => $stats,
            'typeOptions' => $typeOptions,
            'engineOptions' => $engineOptions,
            'marketplaceOptions' => $marketplaceOptions,
            'testResult' => session('monitor_test'),
            'selectedMonitor' => $monitor,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(['price_watch'])],
            'engine_type' => ['required', Rule::in(['simple', 'browser'])],
            'marketplace' => ['nullable', 'string', 'max:100'],
            'target_url' => ['required', 'url', 'max:2000'],
            'check_interval_hours' => ['required', 'integer', 'min:1', 'max:168'],
            'alert_email' => ['nullable', 'email', 'max:255'],
            'should_create_task' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string'],
        ]);

        Monitor::query()->create([
            ...$validated,
            'tracked_field' => 'price',
            'condition_operator' => 'drops_below_last',
            'is_active' => true,
            'should_create_task' => $request->boolean('should_create_task'),
            'created_by' => $request->user()->id,
        ]);

        return redirect()
            ->route('monitors.index')
            ->with('status', 'Monitor bol vytvorený.');
    }

    public function update(Request $request, Monitor $monitor): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(['price_watch'])],
            'engine_type' => ['required', Rule::in(['simple', 'browser'])],
            'marketplace' => ['nullable', 'string', 'max:100'],
            'target_url' => ['required', 'url', 'max:2000'],
            'check_interval_hours' => ['required', 'integer', 'min:1', 'max:168'],
            'alert_email' => ['nullable', 'email', 'max:255'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'should_create_task' => ['nullable', 'boolean'],
        ]);

        $monitor->update([
            ...$validated,
            'is_active' => $request->boolean('is_active'),
            'should_create_task' => $request->boolean('should_create_task'),
        ]);

        return redirect()
            ->route('monitors.index')
            ->with('status', 'Monitor bol aktualizovaný.');
    }

    public function toggle(Monitor $monitor): RedirectResponse
    {
        $monitor->update([
            'is_active' => ! $monitor->is_active,
        ]);

        return redirect()
            ->route('monitors.index')
            ->with('status', $monitor->is_active ? 'Monitor je znovu aktívny.' : 'Monitor je pozastavený.');
    }

    public function test(Monitor $monitor): RedirectResponse
    {
        if ($monitor->engine_type === 'browser') {
            $monitor->update([
                'last_test_status' => 'pending',
                'last_test_message' => 'Browser test je zaradený do queue.',
                'last_test_challenge' => null,
            ]);

            RunBrowserMonitorTestJob::dispatch($monitor->id);

            return redirect()
                ->route('monitors.index')
                ->with('monitor_test', [
                    'monitor_id' => $monitor->id,
                    'ok' => false,
                    'message' => 'Browser test je zaradený do queue. Výsledok sa doplní po spracovaní workerom.',
                    'engine_type' => $monitor->engine_type,
                    'status_code' => 'Queue',
                    'checked_at' => now()->format('d.m.Y H:i:s'),
                    'price_candidates' => [],
                    'html_bytes' => null,
                    'challenge' => null,
                ]);
        }

        try {
            $response = Http::timeout(15)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0 Safari/537.36',
                    'Accept-Language' => 'sk-SK,sk;q=0.9,en;q=0.8',
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
                    'Cache-Control' => 'no-cache',
                    'Pragma' => 'no-cache',
                    'Upgrade-Insecure-Requests' => '1',
                ])
                ->withOptions([
                    'allow_redirects' => true,
                ])
                ->get($monitor->target_url);
        } catch (\Throwable $exception) {
            $monitor->update([
                'last_checked_at' => now(),
                'last_test_status' => 'failed',
                'last_test_message' => 'Test sa nepodaril dokončiť.',
                'last_test_challenge' => null,
            ]);

            return redirect()
                ->route('monitors.index')
                ->with('monitor_test', [
                    'monitor_id' => $monitor->id,
                    'ok' => false,
                    'message' => 'Test sa nepodaril dokončiť.',
                ]);
        }

        $html = $response->body();
        $title = $this->extractTitle($html);
        $priceCandidates = $this->extractPriceCandidates($html);
        $statusCode = $response->status();
        $cloudflareMitigated = filled($response->header('cf-mitigated'));
        $blocked = $statusCode >= 400;

        $message = match (true) {
            $cloudflareMitigated => 'Stránku blokuje Cloudflare challenge. Na tento web bude treba browser render režim.',
            $blocked => "Server stránku neotvoril. HTTP {$statusCode}.",
            $priceCandidates === [] => 'Stránka sa otvorila, ale cenu sa nepodarilo jasne nájsť.',
            default => 'Stránka sa otvorila a našli sa kandidáti na cenu.',
        };
        $testStatus = match (true) {
            $cloudflareMitigated => 'blocked',
            $blocked => 'failed',
            $priceCandidates === [] => 'partial',
            default => 'ok',
        };

        $monitor->update([
            'last_checked_at' => now(),
            'last_test_status' => $testStatus,
            'last_test_message' => $message,
            'last_test_challenge' => $cloudflareMitigated ? 'Cloudflare' : null,
        ]);

        return redirect()
            ->route('monitors.index')
            ->with('monitor_test', [
                'monitor_id' => $monitor->id,
                'ok' => ! $blocked && ! $cloudflareMitigated,
                'message' => $message,
                'title' => $title,
                'status_code' => $statusCode,
                'checked_at' => now()->format('d.m.Y H:i:s'),
                'price_candidates' => array_slice($priceCandidates, 0, 6),
                'html_bytes' => strlen($html),
                'challenge' => $cloudflareMitigated ? 'Cloudflare' : null,
                'engine_type' => $monitor->engine_type,
            ]);
    }

    private function extractTitle(string $html): ?string
    {
        if (preg_match('/<title[^>]*>(.*?)<\/title>/is', $html, $matches) !== 1) {
            return null;
        }

        return trim(html_entity_decode(strip_tags($matches[1]), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    }

    private function extractPriceCandidates(string $html): array
    {
        preg_match_all('/\b\d{1,3}(?:[ .]\d{3})*(?:[.,]\d{1,2})?\s?(?:€|EUR|Kč|CZK|Ft|HUF)\b/u', $html, $matches);

        return array_values(array_unique(array_map(
            static fn (string $value): string => preg_replace('/\s+/', ' ', trim(html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8'))),
            $matches[0] ?? []
        )));
    }
}
