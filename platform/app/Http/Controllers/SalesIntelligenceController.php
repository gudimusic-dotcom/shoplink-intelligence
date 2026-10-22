<?php

namespace App\Http\Controllers;

use App\Models\ImportJob;
use App\Models\SalesOrderLine;
use App\Services\HeurekaProductCatalogImportService;
use App\Services\MobdataOrderImportService;
use App\Services\MobdataOrderLineImportService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesIntelligenceController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorizeAccess($request);

        $filters = $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
            'category_level' => ['nullable', 'string', 'in:2,3,leaf'],
        ]);

        $dateFrom = $filters['date_from'] ?? now()->startOfYear()->toDateString();
        $dateTo = $filters['date_to'] ?? null;
        $categoryLevel = $filters['category_level'] ?? '3';
        $orderLines = $this->orderLinesForPeriod($dateFrom, $dateTo);

        $topSevenPayload = $this->topSevenPayload();

        $dateBounds = SalesOrderLine::query()
            ->selectRaw('min(date(ordered_at)) as first_date, max(date(ordered_at)) as last_date')
            ->first();

        $completedOrders = (clone $orderLines)->distinct('order_code')->count('order_code');
        $eurRevenue = (float) (clone $orderLines)->whereNotNull('net_sales_amount_eur')->sum('net_sales_amount_eur');
        $missingRates = (clone $orderLines)->whereNull('net_sales_amount_eur')->distinct()->pluck('currency')->filter()->values();

        $channelTotals = (clone $orderLines)
            ->select('channel_type', DB::raw('count(distinct order_code) as orders_count'))
            ->groupBy('channel_type')
            ->pluck('orders_count', 'channel_type');

        $marketplaceOrders = (int) ($channelTotals['marketplace'] ?? 0);
        $ownWebOrders = (int) ($channelTotals['own_web'] ?? 0);
        $marketplaceShare = $completedOrders > 0 ? round(($marketplaceOrders / $completedOrders) * 100, 1) : 0;
        $ownWebShare = $completedOrders > 0 ? round(($ownWebOrders / $completedOrders) * 100, 1) : 0;

        $channelRevenueStats = (clone $orderLines)
            ->select('channel_type', DB::raw('count(distinct order_code) as orders_count'), DB::raw('sum(net_sales_amount_eur) as revenue_eur'))
            ->groupBy('channel_type')
            ->orderByDesc('revenue_eur')
            ->get();

        $maxChannelRevenue = max(1, (float) $channelRevenueStats->max('revenue_eur'));

        $countryStats = (clone $orderLines)
            ->select('portal_country', DB::raw('count(distinct order_code) as orders_count'), DB::raw('sum(net_sales_amount_eur) as revenue_eur'))
            ->groupBy('portal_country')
            ->orderByDesc('orders_count')
            ->limit(8)
            ->get();

        $maxCountryOrders = max(1, (int) $countryStats->max('orders_count'));

        $countryMarketplaceMix = (clone $orderLines)
            ->select(
                'portal_country',
                DB::raw('count(distinct order_code) as orders_count'),
                DB::raw("count(distinct case when channel_type = 'marketplace' then order_code end) as marketplace_orders"),
                DB::raw("count(distinct case when channel_type = 'own_web' then order_code end) as own_web_orders"),
                DB::raw('sum(net_sales_amount_eur) as revenue_eur'),
                DB::raw("sum(case when channel_type = 'marketplace' then net_sales_amount_eur else 0 end) as marketplace_revenue_eur"),
                DB::raw("sum(case when channel_type = 'own_web' then net_sales_amount_eur else 0 end) as own_web_revenue_eur")
            )
            ->whereNotNull('portal_country')
            ->groupBy('portal_country')
            ->orderByDesc('orders_count')
            ->get();

        $marketplaceStats = (clone $orderLines)
            ->where('channel_type', 'marketplace')
            ->whereNotNull('marketplace_display')
            ->select('marketplace_display', DB::raw('count(distinct order_code) as orders_count'), DB::raw('sum(net_sales_amount_eur) as revenue_eur'))
            ->groupBy('marketplace_display')
            ->orderByDesc('orders_count')
            ->limit(8)
            ->get();

        $maxMarketplaceOrders = max(1, (int) $marketplaceStats->max('orders_count'));

        $monthExpression = DB::connection()->getDriverName() === 'sqlite'
            ? "strftime('%Y-%m', ordered_at)"
            : "date_format(ordered_at, '%Y-%m')";

        $monthlyStats = (clone $orderLines)
            ->select(DB::raw("{$monthExpression} as month"), DB::raw('count(distinct order_code) as orders_count'), DB::raw('sum(net_sales_amount_eur) as revenue_eur'))
            ->whereNotNull('ordered_at')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $maxMonthlyOrders = max(1, (int) $monthlyStats->max('orders_count'));

        $monthlyChannelMix = (clone $orderLines)
            ->select(
                DB::raw("{$monthExpression} as month"),
                DB::raw('count(distinct order_code) as orders_count'),
                DB::raw("count(distinct case when channel_type = 'marketplace' then order_code end) as marketplace_orders"),
                DB::raw("count(distinct case when channel_type = 'own_web' then order_code end) as own_web_orders"),
                DB::raw('sum(net_sales_amount_eur) as revenue_eur'),
                DB::raw("sum(case when channel_type = 'marketplace' then net_sales_amount_eur else 0 end) as marketplace_revenue_eur"),
                DB::raw("sum(case when channel_type = 'own_web' then net_sales_amount_eur else 0 end) as own_web_revenue_eur")
            )
            ->whereNotNull('ordered_at')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $lineCount = (clone $orderLines)->count();
        $soldPieces = (float) (clone $orderLines)->sum('quantity');
        $productNetRevenue = (float) (clone $orderLines)->whereNotNull('net_sales_amount_eur')->sum('net_sales_amount_eur');
        $productMargin = (float) (clone $orderLines)->whereNotNull('margin_amount_eur')->sum('margin_amount_eur');
        $productMarginRate = $productNetRevenue > 0 ? round(($productMargin / $productNetRevenue) * 100, 1) : 0;

        $topProductMargins = (clone $orderLines)
            ->leftJoin('product_catalog_items', 'sales_order_lines.model', '=', 'product_catalog_items.item_id')
            ->select(
                'sales_order_lines.model',
                'sales_order_lines.brand',
                DB::raw('coalesce(product_catalog_items.product_type, sales_order_lines.category) as category_name'),
                'product_catalog_items.product_name',
                'product_catalog_items.image_url',
                DB::raw('sum(sales_order_lines.quantity) as quantity_sum'),
                DB::raw('sum(sales_order_lines.net_sales_amount_eur) as net_revenue_eur'),
                DB::raw('sum(sales_order_lines.margin_amount_eur) as margin_eur')
            )
            ->whereNotNull('sales_order_lines.margin_amount_eur')
            ->groupBy('sales_order_lines.model', 'sales_order_lines.brand', 'product_catalog_items.product_type', 'sales_order_lines.category', 'product_catalog_items.product_name', 'product_catalog_items.image_url')
            ->orderByDesc('margin_eur')
            ->limit(8)
            ->get();

        $maxProductMargin = max(1, (float) $topProductMargins->max('margin_eur'));

        $categoryStats = (clone $orderLines)
            ->leftJoin('product_catalog_items', 'sales_order_lines.model', '=', 'product_catalog_items.item_id')
            ->select(
                'product_catalog_items.product_type as category_name',
                DB::raw('count(*) as line_count'),
                DB::raw('sum(quantity) as quantity_sum'),
                DB::raw('sum(net_sales_amount_eur) as net_revenue_eur'),
                DB::raw('sum(margin_amount_eur) as margin_eur')
            )
            ->whereNotNull('product_catalog_items.product_type')
            ->groupBy('product_catalog_items.product_type')
            ->orderByDesc('net_revenue_eur')
            ->get()
            ->map(function ($row) use ($categoryLevel) {
                $row->category_name = $this->cleanCategoryName($row->category_name);
                $row->category_name = $this->categoryPathLevel($row->category_name, $categoryLevel);
                $row->category_leaf = $this->categoryLeaf($row->category_name);
                $row->category_parent = $this->categoryParent($row->category_name);
                $netRevenue = (float) $row->net_revenue_eur;
                $row->margin_rate = $netRevenue > 0 ? round(((float) $row->margin_eur / $netRevenue) * 100, 1) : 0;

                return $row;
            })
            ->groupBy('category_name')
            ->map(function ($rows, $categoryName) {
                $first = $rows->first();
                $first->category_name = $categoryName;
                $first->category_leaf = $this->categoryLeaf($categoryName);
                $first->category_parent = $this->categoryParent($categoryName);
                $first->line_count = $rows->sum('line_count');
                $first->quantity_sum = $rows->sum('quantity_sum');
                $first->net_revenue_eur = $rows->sum('net_revenue_eur');
                $first->margin_eur = $rows->sum('margin_eur');
                $netRevenue = (float) $first->net_revenue_eur;
                $first->margin_rate = $netRevenue > 0 ? round(((float) $first->margin_eur / $netRevenue) * 100, 1) : 0;

                return $first;
            })
            ->sortByDesc('net_revenue_eur')
            ->take(10)
            ->values();

        $maxCategoryRevenue = max(1, (float) $categoryStats->max('net_revenue_eur'));

        $latestImports = ImportJob::query()
            ->whereIn('import_type', ['sales_orders', 'sales_order_lines', 'product_catalog'])
            ->latest()
            ->limit(6)
            ->get();

        return view('sales.index', [
            'completedOrders' => $completedOrders,
            'eurRevenue' => $eurRevenue,
            'missingRates' => $missingRates,
            'channelRevenueStats' => $channelRevenueStats,
            'maxChannelRevenue' => $maxChannelRevenue,
            'marketplaceOrders' => $marketplaceOrders,
            'ownWebOrders' => $ownWebOrders,
            'marketplaceShare' => $marketplaceShare,
            'ownWebShare' => $ownWebShare,
            'countryStats' => $countryStats,
            'maxCountryOrders' => $maxCountryOrders,
            'countryMarketplaceMix' => $countryMarketplaceMix,
            'marketplaceStats' => $marketplaceStats,
            'maxMarketplaceOrders' => $maxMarketplaceOrders,
            'monthlyStats' => $monthlyStats,
            'monthlyChannelMix' => $monthlyChannelMix,
            'maxMonthlyOrders' => $maxMonthlyOrders,
            'latestImports' => $latestImports,
            'lineCount' => $lineCount,
            'soldPieces' => $soldPieces,
            'productNetRevenue' => $productNetRevenue,
            'productMargin' => $productMargin,
            'productMarginRate' => $productMarginRate,
            'topProductMargins' => $topProductMargins,
            'maxProductMargin' => $maxProductMargin,
            'categoryStats' => $categoryStats,
            'maxCategoryRevenue' => $maxCategoryRevenue,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'categoryLevel' => $categoryLevel,
            'dateBounds' => $dateBounds,
            ...$topSevenPayload,
        ]);
    }

    public function top30(Request $request): View
    {
        $this->authorizeAccess($request);
        $sort = $request->validate([
            'sort' => ['nullable', 'string', 'in:quantity,revenue,margin,potential'],
        ])['sort'] ?? 'quantity';

        return view('sales.top30', $this->topSevenPayload($sort));
    }

    public function detail(Request $request): View
    {
        $this->authorizeAccess($request);

        $filters = $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
        ]);

        $dateFrom = $filters['date_from'] ?? now()->startOfYear()->toDateString();
        $dateTo = $filters['date_to'] ?? null;
        $orderLines = $this->orderLinesForPeriod($dateFrom, $dateTo);

        $lineCount = (clone $orderLines)->count();
        $soldPieces = (float) (clone $orderLines)->sum('quantity');
        $netRevenue = (float) (clone $orderLines)->whereNotNull('net_sales_amount_eur')->sum('net_sales_amount_eur');
        $purchaseCost = (float) (clone $orderLines)->whereNotNull('purchase_amount_eur')->sum('purchase_amount_eur');
        $margin = (float) (clone $orderLines)->whereNotNull('margin_amount_eur')->sum('margin_amount_eur');
        $marginRate = $netRevenue > 0 ? round(($margin / $netRevenue) * 100, 1) : 0;

        $dateBounds = SalesOrderLine::query()
            ->selectRaw('min(date(ordered_at)) as first_date, max(date(ordered_at)) as last_date')
            ->first();

        $productRows = (clone $orderLines)
            ->leftJoin('product_catalog_items', 'sales_order_lines.model', '=', 'product_catalog_items.item_id')
            ->select(
                'sales_order_lines.model',
                'sales_order_lines.brand',
                DB::raw('coalesce(product_catalog_items.product_type, sales_order_lines.category) as category_name'),
                'product_catalog_items.product_name',
                'product_catalog_items.image_url',
                DB::raw('count(*) as line_count'),
                DB::raw('count(distinct sales_order_lines.order_code) as order_count'),
                DB::raw('sum(sales_order_lines.quantity) as quantity_sum'),
                DB::raw('sum(sales_order_lines.net_sales_amount_eur) as net_revenue_eur'),
                DB::raw('sum(sales_order_lines.purchase_amount_eur) as purchase_eur'),
                DB::raw('sum(sales_order_lines.margin_amount_eur) as margin_eur')
            )
            ->whereNotNull('sales_order_lines.model')
            ->groupBy('sales_order_lines.model', 'sales_order_lines.brand', 'product_catalog_items.product_type', 'sales_order_lines.category', 'product_catalog_items.product_name', 'product_catalog_items.image_url')
            ->orderByDesc('net_revenue_eur')
            ->limit(120)
            ->get()
            ->map(function ($row) {
                $row->category_name = $this->cleanCategoryName($row->category_name);
                $netRevenue = (float) $row->net_revenue_eur;
                $margin = (float) $row->margin_eur;
                $quantity = (float) $row->quantity_sum;
                $marginRate = $netRevenue > 0 ? round(($margin / $netRevenue) * 100, 1) : 0;

                $row->margin_rate = $marginRate;
                $row->revenue_per_piece = $quantity > 0 ? round($netRevenue / $quantity, 2) : 0;
                $row->margin_per_piece = $quantity > 0 ? round($margin / $quantity, 2) : 0;
                $row->signal = $this->productSignal($netRevenue, $marginRate, $quantity);

                return $row;
            });

        $supportProducts = $productRows
            ->filter(fn ($row): bool => (float) $row->quantity_sum >= 2 && (float) $row->margin_rate >= 20 && (float) $row->margin_per_piece > 0)
            ->sortByDesc(fn ($row): float => ((float) $row->margin_rate * 10) + ((float) $row->margin_per_piece * 5) + min((float) $row->quantity_sum, 20))
            ->take(10)
            ->values();

        $topRevenueProducts = $productRows
            ->take(10)
            ->values();

        $highVolumeLowMarginProducts = $productRows
            ->filter(fn ($row): bool => (float) $row->net_revenue_eur > 0 && (float) $row->quantity_sum >= 2 && (float) $row->margin_rate < 12)
            ->sortByDesc('net_revenue_eur')
            ->take(10)
            ->values();

        $negativeMarginProducts = $productRows
            ->filter(fn ($row): bool => (float) $row->margin_eur < 0)
            ->sortBy('margin_eur')
            ->take(8)
            ->values();

        $categoryStats = (clone $orderLines)
            ->leftJoin('product_catalog_items', 'sales_order_lines.model', '=', 'product_catalog_items.item_id')
            ->select(
                DB::raw('coalesce(product_catalog_items.product_type, sales_order_lines.category) as category_name'),
                DB::raw('count(*) as line_count'),
                DB::raw('sum(quantity) as quantity_sum'),
                DB::raw('sum(net_sales_amount_eur) as net_revenue_eur'),
                DB::raw('sum(margin_amount_eur) as margin_eur')
            )
            ->where(function ($query) {
                $query->whereNotNull('product_catalog_items.product_type')
                    ->orWhereNotNull('sales_order_lines.category');
            })
            ->groupBy(DB::raw('coalesce(product_catalog_items.product_type, sales_order_lines.category)'))
            ->orderByDesc('margin_eur')
            ->limit(12)
            ->get()
            ->map(function ($row) {
                $row->category_name = $this->cleanCategoryName($row->category_name);
                $row->category_leaf = $this->categoryLeaf($row->category_name);
                $row->category_parent = $this->categoryParent($row->category_name);
                $netRevenue = (float) $row->net_revenue_eur;
                $row->margin_rate = $netRevenue > 0 ? round(((float) $row->margin_eur / $netRevenue) * 100, 1) : 0;

                return $row;
            });

        $marketplaceProductStats = (clone $orderLines)
            ->select(
                'marketplace_display',
                DB::raw('count(*) as line_count'),
                DB::raw('sum(quantity) as quantity_sum'),
                DB::raw('sum(net_sales_amount_eur) as net_revenue_eur'),
                DB::raw('sum(margin_amount_eur) as margin_eur')
            )
            ->whereNotNull('marketplace_display')
            ->groupBy('marketplace_display')
            ->orderByDesc('net_revenue_eur')
            ->limit(10)
            ->get()
            ->map(function ($row) {
                $netRevenue = (float) $row->net_revenue_eur;
                $row->margin_rate = $netRevenue > 0 ? round(((float) $row->margin_eur / $netRevenue) * 100, 1) : 0;

                return $row;
            });

        $countryProductStats = (clone $orderLines)
            ->select(
                'portal_country',
                DB::raw('count(*) as line_count'),
                DB::raw('sum(quantity) as quantity_sum'),
                DB::raw('sum(net_sales_amount_eur) as net_revenue_eur'),
                DB::raw('sum(margin_amount_eur) as margin_eur')
            )
            ->whereNotNull('portal_country')
            ->groupBy('portal_country')
            ->orderByDesc('net_revenue_eur')
            ->limit(10)
            ->get()
            ->map(function ($row) {
                $netRevenue = (float) $row->net_revenue_eur;
                $row->margin_rate = $netRevenue > 0 ? round(((float) $row->margin_eur / $netRevenue) * 100, 1) : 0;

                return $row;
            });

        return view('sales.detail', [
            'lineCount' => $lineCount,
            'soldPieces' => $soldPieces,
            'netRevenue' => $netRevenue,
            'purchaseCost' => $purchaseCost,
            'margin' => $margin,
            'marginRate' => $marginRate,
            'productRows' => $productRows,
            'supportProducts' => $supportProducts,
            'topRevenueProducts' => $topRevenueProducts,
            'highVolumeLowMarginProducts' => $highVolumeLowMarginProducts,
            'negativeMarginProducts' => $negativeMarginProducts,
            'categoryStats' => $categoryStats,
            'marketplaceProductStats' => $marketplaceProductStats,
            'countryProductStats' => $countryProductStats,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'dateBounds' => $dateBounds,
        ]);
    }

    public function categoryReport(Request $request): View
    {
        $this->authorizeAccess($request);

        $filters = $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
            'category_level' => ['nullable', 'string', 'in:2,3,leaf'],
            'channel' => ['nullable', 'string', 'in:all,marketplace,own_web'],
        ]);

        $dateFrom = $filters['date_from'] ?? now()->startOfYear()->toDateString();
        $dateTo = $filters['date_to'] ?? null;
        $categoryLevel = $filters['category_level'] ?? '3';
        $channel = $filters['channel'] ?? 'all';

        $salesBySku = $this->orderLinesForPeriod($dateFrom, $dateTo)
            ->when($channel !== 'all', fn (Builder $query): Builder => $query->where('channel_type', $channel))
            ->whereNotNull('model')
            ->select(
                DB::raw('trim(model) as sku'),
                DB::raw('sum(net_sales_amount_eur) as revenue_eur'),
                DB::raw('sum(margin_amount_eur) as profit_eur')
            )
            ->groupBy(DB::raw('trim(model)'))
            ->get()
            ->keyBy('sku');

        $categoryRows = DB::table('product_catalog_items')
            ->select('item_id', 'product_type')
            ->whereNotNull('product_type')
            ->where('product_type', '<>', '')
            ->get()
            ->map(function ($item) use ($salesBySku, $categoryLevel) {
                $rawCategory = $this->cleanCategoryName($item->product_type);
                $categoryName = $this->categoryPathLevel($rawCategory, $categoryLevel);
                $sales = $salesBySku->get(trim((string) $item->item_id));

                return [
                    'category_name' => $categoryName ?: 'Bez kategórie',
                    'sku' => trim((string) $item->item_id),
                    'revenue_eur' => (float) ($sales?->revenue_eur ?? 0),
                    'profit_eur' => (float) ($sales?->profit_eur ?? 0),
                ];
            })
            ->groupBy('category_name')
            ->map(function ($items, $categoryName) {
                $revenue = (float) $items->sum('revenue_eur');
                $profit = (float) $items->sum('profit_eur');
                $activeSkuCount = $items->count();
                $skuRevenue = $items->sortByDesc('revenue_eur')->values();
                $targetRevenue = $revenue * 0.8;
                $runningRevenue = 0.0;
                $skuTo80Count = 0;

                if ($revenue > 0) {
                    foreach ($skuRevenue as $sku) {
                        $skuTo80Count++;
                        $runningRevenue += (float) $sku['revenue_eur'];

                        if ($runningRevenue >= $targetRevenue) {
                            break;
                        }
                    }
                }

                return (object) [
                    'category_name' => $categoryName,
                    'revenue_eur' => $revenue,
                    'profit_eur' => $profit,
                    'margin_rate' => $revenue > 0 ? round(($profit / $revenue) * 100, 1) : null,
                    'active_sku_count' => $activeSkuCount,
                    'revenue_per_sku' => $activeSkuCount > 0 ? $revenue / $activeSkuCount : 0,
                    'sku_to_80_count' => $skuTo80Count,
                    'sku_to_80_percent' => $revenue > 0 && $activeSkuCount > 0 ? ($skuTo80Count / $activeSkuCount) * 100 : null,
                ];
            })
            ->sortByDesc('revenue_eur')
            ->values();

        $dateBounds = SalesOrderLine::query()
            ->selectRaw('min(date(ordered_at)) as first_date, max(date(ordered_at)) as last_date')
            ->first();

        return view('sales.category-report', [
            'categoryRows' => $categoryRows,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'categoryLevel' => $categoryLevel,
            'channel' => $channel,
            'dateBounds' => $dateBounds,
        ]);
    }

    public function import(Request $request, MobdataOrderImportService $importService): RedirectResponse
    {
        $this->authorizeAccess($request);

        $validated = $request->validate([
            'orders_file' => ['required', 'file', 'mimes:xlsx', 'max:20480'],
        ]);

        $job = $importService->import($validated['orders_file'], $request->user()->id);

        return redirect()
            ->route('sales.index')
            ->with('status', "Import objednávok dokončený: {$job->imported_rows} nových, {$job->skipped_rows} preskočených, {$job->warning_count} upozornení.");
    }

    public function importLines(Request $request, MobdataOrderLineImportService $importService): RedirectResponse
    {
        $this->authorizeAccess($request);

        $validated = $request->validate([
            'order_lines_file' => ['required', 'file', 'mimes:xlsx', 'max:20480'],
        ]);

        $job = $importService->import($validated['order_lines_file'], $request->user()->id);

        return redirect()
            ->route('sales.index')
            ->with('status', "Import riadkov dokončený: {$job->imported_rows} nových, {$job->skipped_rows} preskočených, {$job->warning_count} upozornení.");
    }

    public function importProducts(Request $request, HeurekaProductCatalogImportService $importService): RedirectResponse
    {
        $this->authorizeAccess($request);

        $validated = $request->validate([
            'products_file' => ['required', 'file', 'max:51200'],
        ]);

        $job = $importService->import($validated['products_file'], $request->user()->id);

        return redirect()
            ->route('sales.index')
            ->with('status', "Import produktov dokončený: {$job->imported_rows} položiek, {$job->skipped_rows} preskočených.");
    }

    private function orderLinesForPeriod(?string $dateFrom, ?string $dateTo): Builder
    {
        return SalesOrderLine::query()
            ->when($dateFrom, fn (Builder $query): Builder => $query->whereDate('ordered_at', '>=', $dateFrom))
            ->when($dateTo, fn (Builder $query): Builder => $query->whereDate('ordered_at', '<=', $dateTo));
    }

    private function topProductsForDates($from, $to)
    {
        $catalogItems = DB::table('product_catalog_items')
            ->select(DB::raw('trim(item_id) as item_id'), DB::raw('max(product_name) as product_name'), DB::raw('max(image_url) as image_url'))
            ->groupBy(DB::raw('trim(item_id)'));

        return SalesOrderLine::query()
            ->leftJoinSub($catalogItems, 'product_catalog_items', function ($join): void {
                $join->on(DB::raw('trim(sales_order_lines.model)'), '=', DB::raw('trim(product_catalog_items.item_id)'));
            })
            ->select(
                DB::raw('max(sales_order_lines.model) as model'),
                DB::raw('max(sales_order_lines.brand) as brand'),
                DB::raw('max(product_catalog_items.product_name) as product_name'),
                DB::raw('max(product_catalog_items.image_url) as image_url'),
                DB::raw('count(distinct sales_order_lines.order_code) as order_count'),
                DB::raw('sum(sales_order_lines.quantity) as quantity_sum'),
                DB::raw('sum(sales_order_lines.net_sales_amount_eur) as net_revenue_eur'),
                DB::raw('sum(sales_order_lines.margin_amount_eur) as margin_eur')
            )
            ->whereBetween('sales_order_lines.ordered_at', [$from, $to])
            ->whereNotNull('sales_order_lines.model')
            ->groupBy(DB::raw('trim(sales_order_lines.model)'))
            ->get();
    }

    private function topSevenPayload(string $sort = 'quantity'): array
    {
        $topSevenFrom = now()->subDays(29)->startOfDay();
        $topSevenTo = now()->endOfDay();
        $previousSevenFrom = now()->subDays(59)->startOfDay();
        $previousSevenTo = now()->subDays(30)->endOfDay();
        $topSevenProducts = $this->topProductsForDates($topSevenFrom, $topSevenTo);
        $previousSevenQuantities = SalesOrderLine::query()
            ->select(DB::raw('trim(model) as model'), DB::raw('sum(quantity) as quantity_sum'))
            ->whereBetween('ordered_at', [$previousSevenFrom, $previousSevenTo])
            ->whereNotNull('model')
            ->groupBy(DB::raw('trim(model)'))
            ->pluck('quantity_sum', 'model');
        $topSevenProducts->each(function ($row) use ($previousSevenQuantities): void {
            $previousQuantity = (float) ($previousSevenQuantities[trim((string) $row->model)] ?? 0);
            $currentQuantity = (float) $row->quantity_sum;
            $row->quantity_change_percent = $previousQuantity > 0
                ? round((($currentQuantity - $previousQuantity) / $previousQuantity) * 100, 1)
                : null;
            $row->average_price_eur = $currentQuantity > 0 && $row->net_revenue_eur !== null
                ? round((float) $row->net_revenue_eur / $currentQuantity, 2)
                : null;
            $row->margin_rate = (float) $row->net_revenue_eur > 0
                ? round(((float) $row->margin_eur / (float) $row->net_revenue_eur) * 100, 1)
                : null;
        });

        $maxRecentQuantity = max(1, (float) $topSevenProducts->max('quantity_sum'));
        $topSevenProducts->each(function ($row) use ($maxRecentQuantity): void {
            $volumeScore = min(((float) $row->quantity_sum / $maxRecentQuantity) * 45, 45);
            $growthScore = $row->quantity_change_percent !== null
                ? min(max((float) $row->quantity_change_percent, 0) / 100 * 30, 30)
                : 0;
            $marginScore = $row->margin_rate !== null
                ? min(max((float) $row->margin_rate, 0) / 50 * 25, 25)
                : 0;
            $row->potential_score = round($volumeScore + $growthScore + $marginScore);
        });

        $sortLabels = [
            'quantity' => 'Predané kusy',
            'revenue' => 'Obrat',
            'margin' => 'Marža',
            'potential' => 'Potenciál',
        ];
        $sortFields = [
            'quantity' => 'quantity_sum',
            'revenue' => 'net_revenue_eur',
            'margin' => 'margin_eur',
            'potential' => 'potential_score',
        ];
        $sortField = $sortFields[$sort] ?? $sortFields['quantity'];
        $topSevenProducts = $topSevenProducts
            ->sortByDesc(fn ($row) => (float) ($row->{$sortField} ?? 0))
            ->take(30)
            ->values();

        return [
            'topSevenProducts' => $topSevenProducts,
            'topSevenMaxQuantity' => max(1, (float) $topSevenProducts->max('quantity_sum')),
            'topSevenFrom' => $topSevenFrom,
            'topSevenTo' => $topSevenTo,
            'topSevenSort' => $sort,
            'topSevenSortLabel' => $sortLabels[$sort] ?? $sortLabels['quantity'],
        ];
    }

    private function productSignal(float $netRevenue, float $marginRate, float $quantity): array
    {
        if ($netRevenue > 0 && $marginRate < 0) {
            return ['label' => 'Strata', 'class' => 'sl-status-blocked'];
        }

        if ($netRevenue >= 500 && $marginRate < 12) {
            return ['label' => 'Cena', 'class' => 'sl-status-in_progress'];
        }

        if ($quantity >= 5 && $marginRate >= 25) {
            return ['label' => 'Podporiť', 'class' => 'sl-status-done'];
        }

        return ['label' => 'Stabilné', 'class' => 'sl-status-new'];
    }

    private function cleanCategoryName(?string $categoryName): ?string
    {
        if ($categoryName === null) {
            return null;
        }

        return trim((string) preg_replace('/^\s*ESHOP\s*>\s*/iu', '', $categoryName));
    }

    private function categoryLeaf(?string $categoryName): ?string
    {
        if ($categoryName === null) {
            return null;
        }

        $parts = array_values(array_filter(array_map('trim', explode('>', $categoryName))));

        return $parts !== [] ? end($parts) : $categoryName;
    }

    private function categoryParent(?string $categoryName): ?string
    {
        if ($categoryName === null || ! str_contains($categoryName, '>')) {
            return null;
        }

        $parts = array_values(array_filter(array_map('trim', explode('>', $categoryName))));
        array_pop($parts);

        return $parts !== [] ? implode(' > ', $parts) : null;
    }

    private function categoryPathLevel(?string $categoryName, string $level): ?string
    {
        if ($categoryName === null || $level === 'leaf') {
            return $categoryName;
        }

        $parts = array_values(array_filter(array_map('trim', explode('>', $categoryName))));
        $limit = (int) $level;

        return $parts !== [] ? implode(' > ', array_slice($parts, 0, $limit)) : $categoryName;
    }

    private function authorizeAccess(Request $request): void
    {
        abort_unless($request->user()?->canAccessSalesIntelligence(), 403);
    }
}
