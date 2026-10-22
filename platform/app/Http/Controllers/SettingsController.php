<?php

namespace App\Http\Controllers;

use App\Models\ExchangeRate;
use App\Models\ProductStatItem;
use App\Models\ProductStatIncomingItem;
use App\Models\ProductStatSupplierSetting;
use App\Models\ProductStatSupplierStock;
use App\Models\SalesOrder;
use App\Models\SalesOrderLine;
use App\Models\TaskCategory;
use Illuminate\Support\Facades\Config;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingsController extends Controller
{
    public function index(): View
    {
        $rates = ExchangeRate::query()->orderBy('currency')->get();
        $taskCategories = TaskCategory::query()
            ->withCount('tasks')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
        $supplierSettings = ProductStatSupplierSetting::query()
            ->orderBy('supplier_name')
            ->get()
            ->keyBy(fn (ProductStatSupplierSetting $setting) => mb_strtolower($setting->supplier_name));
        $supplierNames = ProductStatItem::query()
            ->pluck('supplier_raw')
            ->flatMap(fn ($supplierRaw) => $this->extractSupplierNames($supplierRaw))
            ->merge($supplierSettings->pluck('supplier_name'))
            ->filter()
            ->unique()
            ->sort()
            ->values();
        $supplierStockMeta = ProductStatSupplierStock::query()
            ->get(['supplier_name', 'checked_at'])
            ->groupBy(fn (ProductStatSupplierStock $stock) => mb_strtolower($stock->supplier_name))
            ->map(fn ($stocks) => [
                'count' => $stocks->count(),
                'checked_at' => $stocks->max('checked_at')?->format('d.m.Y H:i'),
            ]);
        $fddIncomingItems = ProductStatIncomingItem::query()->where('supplier_name', 'Fdd')->get(['qty_incoming', 'created_at']);

        return view('settings.index', [
            'rates' => $rates,
            'taskCategories' => $taskCategories,
            'webPushPublicKey' => (string) Config::get('services.web_push.public_key', ''),
            'fddIncomingCount' => ProductStatIncomingItem::query()->where('supplier_name', 'Fdd')->count(),
            'fddIncomingUnits' => (int) ProductStatIncomingItem::query()->where('supplier_name', 'Fdd')->sum('qty_incoming'),
            'supplierPortalLinks' => $supplierNames
                ->map(function (string $supplierName) use ($supplierSettings, $supplierStockMeta, $fddIncomingItems) {
                    $setting = $supplierSettings->get(mb_strtolower($supplierName));
                    $stockMeta = $supplierStockMeta->get(mb_strtolower($supplierName), []);

                    return [
                        'supplier' => $supplierName,
                        'portal_url_template' => $setting?->portal_url_template,
                        'stock_feed_url' => $setting?->stock_feed_url,
                        'incoming_feed_url' => $setting?->incoming_feed_url,
                        'target_coverage_days' => $setting?->target_coverage_days ?? 45,
                        'stock_count' => $stockMeta['count'] ?? 0,
                        'stock_checked_at' => $stockMeta['checked_at'] ?? null,
                        'incoming_count' => $supplierName === 'Fdd' ? $fddIncomingItems->count() : 0,
                        'incoming_units' => $supplierName === 'Fdd' ? (int) $fddIncomingItems->sum('qty_incoming') : 0,
                        'incoming_checked_at' => $supplierName === 'Fdd' ? $fddIncomingItems->max('created_at')?->format('d.m.Y H:i') : null,
                        'has_data' => filled($setting?->portal_url_template)
                            || filled($setting?->stock_feed_url)
                            || filled($setting?->incoming_feed_url)
                            || (($stockMeta['count'] ?? 0) > 0)
                            || ($supplierName === 'Fdd' && $fddIncomingItems->count() > 0),
                    ];
                }),
        ]);
    }

    public function updateTaskCenter(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'dashboard_task_window_days' => ['required', 'integer', 'min:1', 'max:14'],
            'web_push_notif_new_task' => ['nullable', 'boolean'],
        ]);

        $request->user()->update([
            'dashboard_task_window_days' => $validated['dashboard_task_window_days'],
            'web_push_notif_new_task' => (bool) ($validated['web_push_notif_new_task'] ?? false),
        ]);

        return redirect()
            ->route('settings.index')
            ->with('status', 'Task Center nastavenia boli uložené.');
    }

    public function updateProductStatsSuppliers(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'suppliers' => ['required', 'array'],
            'suppliers.*.supplier_name' => ['required', 'string', 'max:255'],
            'suppliers.*.portal_url_template' => ['nullable', 'string', 'max:1000'],
            'suppliers.*.stock_feed_url' => ['nullable', 'url', 'max:2000'],
            'suppliers.*.incoming_feed_url' => ['nullable', 'url', 'max:2000'],
            'suppliers.*.target_coverage_days' => ['required', 'integer', 'min:1', 'max:365'],
        ]);

        foreach ($validated['suppliers'] as $supplier) {
            ProductStatSupplierSetting::query()->updateOrCreate(
                ['supplier_name' => $supplier['supplier_name']],
                [
                    'portal_url_template' => $supplier['portal_url_template'] ?? null,
                    'stock_feed_url' => $supplier['stock_feed_url'] ?? null,
                    'incoming_feed_url' => $supplier['incoming_feed_url'] ?? null,
                    'target_coverage_days' => (int) $supplier['target_coverage_days'],
                ],
            );
        }

        return redirect()
            ->route('settings.index')
            ->with('status', 'Product Stats dodávatelia boli uložené.');
    }

    public function updateRates(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'rates' => ['required', 'array'],
            'rates.*.currency' => ['required', 'string', 'max:10'],
            'rates.*.rate_to_eur' => ['nullable', 'numeric', 'gt:0'],
        ]);

        foreach ($validated['rates'] as $rate) {
            ExchangeRate::query()->updateOrCreate(
                ['currency' => $rate['currency']],
                ['rate_to_eur' => isset($rate['rate_to_eur']) ? round((float) $rate['rate_to_eur'], 2) : null],
            );
        }

        ExchangeRate::query()
            ->whereNotNull('rate_to_eur')
            ->where('rate_to_eur', '>', 0)
            ->get(['currency', 'rate_to_eur'])
            ->each(function (ExchangeRate $rate): void {
                SalesOrder::query()
                    ->where('currency', $rate->currency)
                    ->update([
                        'gross_amount_eur' => DB::raw('ROUND(gross_amount / '.(float) $rate->rate_to_eur.', 2)'),
                    ]);

                SalesOrderLine::query()
                    ->where('currency', $rate->currency)
                    ->update([
                        'net_sales_amount_eur' => DB::raw('ROUND(net_sales_amount / '.(float) $rate->rate_to_eur.', 2)'),
                        'purchase_amount_eur' => DB::raw('CASE WHEN purchase_amount IS NULL THEN NULL ELSE ROUND(purchase_amount / '.(float) $rate->rate_to_eur.', 2) END'),
                        'margin_amount_eur' => DB::raw('CASE WHEN margin_amount IS NULL THEN NULL ELSE ROUND(margin_amount / '.(float) $rate->rate_to_eur.', 2) END'),
                    ]);
            });

        $knownCurrencies = ExchangeRate::query()
            ->whereNotNull('rate_to_eur')
            ->where('rate_to_eur', '>', 0)
            ->pluck('currency');

        SalesOrder::query()
            ->whereNotIn('currency', $knownCurrencies)
            ->update(['gross_amount_eur' => null]);

        SalesOrderLine::query()
            ->whereNotIn('currency', $knownCurrencies)
            ->update([
                'net_sales_amount_eur' => null,
                'purchase_amount_eur' => null,
                'margin_amount_eur' => null,
            ]);

        return redirect()
            ->route('settings.index')
            ->with('status', 'Kurzy boli uložené a EUR hodnoty objednávok boli prepočítané.');
    }

    private function extractSupplierNames(?string $supplierRaw): array
    {
        $raw = trim((string) $supplierRaw);

        if ($raw === '') {
            return [];
        }

        return collect(preg_split('/\s*,\s*/', $raw, -1, PREG_SPLIT_NO_EMPTY))
            ->map(function ($segment) {
                preg_match_all('/[\p{L}\p{N}]+/u', trim((string) $segment), $matches);

                $parts = collect($matches[0] ?? [])
                    ->filter(fn ($part) => preg_match('/\p{L}/u', $part) === 1)
                    ->values()
                    ->all();

                return $parts === [] ? null : mb_convert_case(mb_strtolower(implode(' ', $parts)), MB_CASE_TITLE, 'UTF-8');
            })
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}
