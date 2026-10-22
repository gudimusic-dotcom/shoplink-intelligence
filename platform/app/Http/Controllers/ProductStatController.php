<?php

namespace App\Http\Controllers;

use App\Models\ProductStatImport;
use App\Models\ProductStatIncomingItem;
use App\Models\ProductStatItem;
use App\Models\ProductStatSupplierSetting;
use App\Models\ProductStatSupplierStock;
use App\Models\SalesOrderLine;
use App\Services\ProductStatsIncomingImportService;
use App\Services\ProductStatsImportService;
use App\Services\HurtelStockImportService;
use App\Services\FddStockImportService;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ProductStatController extends Controller
{
    public function __construct(
        private readonly ProductStatsImportService $importService,
        private readonly ProductStatsIncomingImportService $incomingImportService,
        private readonly HurtelStockImportService $hurtelStockImportService,
        private readonly FddStockImportService $fddStockImportService,
    ) {}

    public function index(Request $request): View
    {
        $this->authorizeAccess($request);

        $filters = $request->validate([
            'supplier' => ['nullable', 'string', 'max:255'],
            'supplier_stock' => ['nullable', 'string', 'in:all,in_stock,out_of_stock,unknown'],
            'status' => ['nullable', 'string', 'in:all,order,watch,stock,clearance,lost_sales,price_check'],
            'q' => ['nullable', 'string', 'max:255'],
            'stock_field' => ['nullable', 'string', 'in:s1,s2'],
            'stock_operator' => ['nullable', 'string', 'in:<,<=,=,>=,>'],
            'stock_value' => ['nullable', 'integer', 'min:0'],
            'priority' => ['nullable', 'string', 'in:0,1'],
        ]);

        return view('product-stats.index', $this->buildIndexPayload($request, $filters));
    }

    public function preparation(Request $request): View
    {
        $this->authorizeAccess($request);

        $filters = $request->validate([
            'supplier' => ['nullable', 'string', 'max:255'],
        ]);

        return view('product-stats.preparation', $this->buildPreparationPayload($filters));
    }

    public function import(Request $request): RedirectResponse
    {
        $this->authorizeAccess($request);

        $validated = $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx'],
            'period_from' => ['nullable', 'date'],
            'period_to' => ['nullable', 'date', 'after_or_equal:period_from'],
        ]);

        $parsed = $this->importService->parse($validated['file']);

        DB::transaction(function () use ($request, $validated, $parsed) {
            ProductStatImport::query()->delete();

            $import = ProductStatImport::query()->create([
                'source_name' => $validated['file']->getClientOriginalName(),
                'sheet_name' => $parsed['sheet_name'],
                'period_from' => $validated['period_from'] ?? null,
                'period_to' => $validated['period_to'] ?? null,
                'row_count' => count($parsed['rows']),
                'imported_by' => $request->user()->id,
            ]);

            foreach (array_chunk($parsed['rows'], 250) as $chunk) {
                $rows = array_map(function (array $row) use ($import) {
                    return [
                        ...$row,
                        'raw_payload' => isset($row['raw_payload']) ? json_encode($row['raw_payload'], JSON_UNESCAPED_UNICODE) : null,
                        'import_id' => $import->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }, $chunk);

                ProductStatItem::query()->insert($rows);
            }
        });

        return redirect()
            ->route('product-stats.index')
            ->with('status', 'Product Stats import bol spracovaný.');
    }

    public function importFddIncoming(Request $request): RedirectResponse
    {
        $this->authorizeAccess($request);

        $validated = $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt'],
        ]);

        $rows = $this->incomingImportService->parseFddCsv($validated['file']);

        DB::transaction(function () use ($request, $validated, $rows) {
            ProductStatIncomingItem::query()
                ->where('supplier_name', 'Fdd')
                ->delete();

            foreach (array_chunk($rows, 250) as $chunk) {
                ProductStatIncomingItem::query()->insert(array_map(fn (array $row) => [
                    ...$row,
                    'source_name' => $validated['file']->getClientOriginalName(),
                    'imported_by' => $request->user()->id,
                    'raw_payload' => json_encode($row['raw_payload'], JSON_UNESCAPED_UNICODE),
                    'created_at' => now(),
                    'updated_at' => now(),
                ], $chunk));
            }
        });

        return redirect()
            ->route('product-stats.index')
            ->with('status', 'FDD incoming import bol spracovaný.');
    }

    public function importFddIncomingFromUrl(Request $request): RedirectResponse
    {
        $this->authorizeAccess($request);

        $validated = $request->validate([
            'import_incoming_supplier_index' => ['required', 'integer', 'min:0'],
            'suppliers' => ['required', 'array'],
        ]);
        $index = $validated['import_incoming_supplier_index'];
        $incomingFeedUrl = $request->input('suppliers.'.$index.'.incoming_feed_url');
        validator(
            ['supplier' => $request->input('suppliers.'.$index.'.supplier_name'), 'incoming_feed_url' => $incomingFeedUrl],
            [
                'supplier' => ['required', 'string', 'in:Fdd'],
                'incoming_feed_url' => ['required', 'url', 'max:2000'],
            ],
        )->validate();
        $temporaryPath = null;

        try {
            $response = Http::timeout(120)->retry(2, 1000)->get($incomingFeedUrl)->throw();
            $temporaryPath = tempnam(sys_get_temp_dir(), 'fdd-incoming-');
            if ($temporaryPath === false || file_put_contents($temporaryPath, $response->body()) === false) {
                throw new \RuntimeException('Feed sa nepodarilo uložiť na spracovanie.');
            }

            $file = new UploadedFile(
                $temporaryPath,
                'fdd-incoming.csv',
                $response->header('Content-Type'),
                null,
                true,
            );
            $rows = $this->incomingImportService->parseFddCsv($file);

            DB::transaction(function () use ($request, $incomingFeedUrl, $rows): void {
                ProductStatIncomingItem::query()->where('supplier_name', 'Fdd')->delete();

                foreach (array_chunk($rows, 250) as $chunk) {
                    ProductStatIncomingItem::query()->insert(array_map(fn (array $row) => [
                        ...$row,
                        'source_name' => mb_substr($incomingFeedUrl, 0, 255),
                        'imported_by' => $request->user()->id,
                        'raw_payload' => json_encode($row['raw_payload'], JSON_UNESCAPED_UNICODE),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ], $chunk));
                }

                ProductStatSupplierSetting::query()->updateOrCreate(
                    ['supplier_name' => 'Fdd'],
                    ['incoming_feed_url' => $incomingFeedUrl],
                );
            });
        } catch (\Throwable $exception) {
            return redirect()
                ->route('settings.index')
                ->withErrors(['incoming_feed_url' => 'Feed sa nepodarilo načítať: '.$exception->getMessage()]);
        } finally {
            if ($temporaryPath !== null) {
                @unlink($temporaryPath);
            }
        }

        return redirect()
            ->route('settings.index')
            ->with('status', 'FDD objednateľný feed bol aktualizovaný z URL: '.count($rows).' produktov.');
    }

    public function importHurtelStock(Request $request): RedirectResponse
    {
        $this->authorizeAccess($request);

        $validated = $request->validate([
            'file' => ['required', 'file', 'mimes:xml', 'max:20480'],
        ]);

        $rows = $this->hurtelStockImportService->parse($validated['file']);
        $checkedAt = now();

        DB::transaction(function () use ($validated, $rows, $checkedAt): void {
            ProductStatSupplierStock::query()->where('supplier_name', 'Hurtel')->delete();

            foreach (array_chunk($rows, 500) as $chunk) {
                ProductStatSupplierStock::query()->insert(array_map(fn (array $row) => [
                    ...$row,
                    'source_name' => $validated['file']->getClientOriginalName(),
                    'checked_at' => $checkedAt,
                    'created_at' => $checkedAt,
                    'updated_at' => $checkedAt,
                ], $chunk));
            }
        });

        return redirect()
            ->route('product-stats.index')
            ->with('status', 'Hurtel skladový feed bol importovaný: '.count($rows).' produktov.');
    }

    public function importFddStock(Request $request): RedirectResponse
    {
        $this->authorizeAccess($request);

        $validated = $request->validate([
            'file' => ['required', 'file', 'mimes:zip,csv,txt', 'max:51200'],
        ]);

        $rows = $this->fddStockImportService->parse($validated['file']);
        $checkedAt = now();

        DB::transaction(function () use ($validated, $rows, $checkedAt): void {
            ProductStatSupplierStock::query()->where('supplier_name', 'Fdd')->delete();

            foreach (array_chunk($rows, 500) as $chunk) {
                ProductStatSupplierStock::query()->insert(array_map(fn (array $row) => [
                    ...$row,
                    'source_name' => $validated['file']->getClientOriginalName(),
                    'checked_at' => $checkedAt,
                    'created_at' => $checkedAt,
                    'updated_at' => $checkedAt,
                ], $chunk));
            }
        });

        return redirect()
            ->route('product-stats.index')
            ->with('status', 'FDD skladový feed bol importovaný: '.count($rows).' produktov.');
    }

    public function importSupplierStockFromUrl(Request $request): RedirectResponse
    {
        $this->authorizeAccess($request);

        $validated = $request->validate([
            'import_stock_supplier_index' => ['required', 'integer', 'min:0'],
            'suppliers' => ['required', 'array'],
        ]);

        $supplier = $request->input('suppliers.'.$validated['import_stock_supplier_index'].'.supplier_name');
        $url = $request->input('suppliers.'.$validated['import_stock_supplier_index'].'.stock_feed_url');

        validator(
            ['supplier' => $supplier, 'stock_feed_url' => $url],
            [
                'supplier' => ['required', 'string', 'in:Fdd,Hurtel'],
                'stock_feed_url' => ['required', 'url', 'max:2000'],
            ],
        )->validate();

        $temporaryPath = null;

        try {
            $response = Http::timeout(120)->retry(2, 1000)->get($url)->throw();
            $contentType = strtolower((string) $response->header('Content-Type'));
            $urlPath = strtolower((string) parse_url($url, PHP_URL_PATH));
            $extension = $supplier === 'Hurtel'
                ? 'xml'
                : (str_ends_with($urlPath, '.zip') || str_contains($contentType, 'zip') ? 'zip' : 'csv');

            $temporaryPath = tempnam(sys_get_temp_dir(), 'supplier-stock-');
            if ($temporaryPath === false || file_put_contents($temporaryPath, $response->body()) === false) {
                throw new \RuntimeException('Feed sa nepodarilo uložiť na spracovanie.');
            }

            $file = new UploadedFile(
                $temporaryPath,
                'supplier-stock.'.$extension,
                $response->header('Content-Type'),
                null,
                true,
            );
            $rows = $supplier === 'Hurtel'
                ? $this->hurtelStockImportService->parse($file)
                : $this->fddStockImportService->parse($file);
            $checkedAt = now();

            DB::transaction(function () use ($supplier, $url, $rows, $checkedAt): void {
                ProductStatSupplierStock::query()->where('supplier_name', $supplier)->delete();

                foreach (array_chunk($rows, 500) as $chunk) {
                    ProductStatSupplierStock::query()->insert(array_map(fn (array $row) => [
                        ...$row,
                        'source_name' => mb_substr($url, 0, 255),
                        'checked_at' => $checkedAt,
                        'created_at' => $checkedAt,
                        'updated_at' => $checkedAt,
                    ], $chunk));
                }

                ProductStatSupplierSetting::query()->updateOrCreate(
                    ['supplier_name' => $supplier],
                    ['stock_feed_url' => $url],
                );
            });
        } catch (\Throwable $exception) {
            return redirect()
                ->route('settings.index')
                ->withErrors(['stock_feed_url' => 'Feed sa nepodarilo načítať: '.$exception->getMessage()]);
        } finally {
            if ($temporaryPath !== null) {
                @unlink($temporaryPath);
            }
        }

        return redirect()
            ->route('settings.index')
            ->with('status', $supplier.' skladový feed bol aktualizovaný z URL: '.count($rows).' produktov.');
    }

    public function clearFddIncoming(Request $request): RedirectResponse
    {
        $this->authorizeAccess($request);

        ProductStatIncomingItem::query()
            ->where('supplier_name', 'Fdd')
            ->delete();

        return redirect()
            ->route('settings.index')
            ->with('status', 'FDD objednaný tovar bol vymazaný.');
    }

    public function clearSupplierStock(Request $request, string $supplier): RedirectResponse
    {
        $this->authorizeAccess($request);
        abort_unless(in_array($supplier, ['Fdd', 'Hurtel'], true), 404);

        ProductStatSupplierStock::query()
            ->where('supplier_name', $supplier)
            ->delete();

        return redirect()
            ->route('settings.index')
            ->with('status', $supplier.' sklad dodávateľa bol vymazaný.');
    }

    public function exportCsv(Request $request): Response
    {
        $this->authorizeAccess($request);

        $validated = $request->validate([
            'supplier' => ['required', 'string', 'max:255'],
            'q' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'in:all,order,watch,stock,clearance,lost_sales,price_check'],
            'stock_field' => ['nullable', 'string', 'in:s1,s2'],
            'stock_operator' => ['nullable', 'string', 'in:<,<=,=,>=,>'],
            'stock_value' => ['nullable', 'integer', 'min:0'],
            'quantities' => ['nullable', 'array'],
            'quantities.*' => ['nullable', 'integer', 'min:0'],
        ]);

        $payload = $this->buildIndexPayload($request, $validated);
        $group = $payload['supplierGroups']->firstWhere('supplier', $validated['supplier']);

        abort_unless($group, 404);

        $rows = collect($group['items'])
            ->map(function ($item) use ($validated) {
                $qty = (int) ($validated['quantities'][$item->sku] ?? 0);

                return [
                    'code' => (string) ($item->ean ?? ''),
                    'qty' => $qty,
                ];
            })
            ->unique(fn ($row) => $row['code'].'|'.$row['qty'])
            ->filter(fn ($row) => $row['code'] !== '' && $row['qty'] > 0)
            ->values();

        $filename = 'product-order-'.str($validated['supplier'])->slug('-').'-'.now()->format('Ymd-His').'.csv';

        $content = $rows->prepend(['code' => 'SkladovyKod', 'qty' => 'qty'])
            ->map(fn ($row) => $row['code'].','.$row['qty'])
            ->implode("\n");

        return response($content, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    private function buildIndexPayload(Request $request, array $filters): array
    {
        $latestImport = ProductStatImport::query()
            ->with('importedBy')
            ->latest()
            ->first();

        $search = trim((string) ($filters['q'] ?? ''));
        $supplierFilter = trim((string) ($filters['supplier'] ?? ''));
        $supplierStockFilter = (string) ($filters['supplier_stock'] ?? 'all');
        $statusFilter = (string) ($filters['status'] ?? 'all');
        $stockField = (string) ($filters['stock_field'] ?? '');
        $stockOperator = (string) ($filters['stock_operator'] ?? '');
        $stockValue = isset($filters['stock_value']) ? (int) $filters['stock_value'] : null;
        $priorityOnly = (string) ($filters['priority'] ?? '0') === '1';
        $supplierSettings = ProductStatSupplierSetting::query()
            ->get()
            ->keyBy(fn (ProductStatSupplierSetting $setting) => mb_strtolower($setting->supplier_name));
        $incomingByEan = ProductStatIncomingItem::query()
            ->selectRaw('ean, SUM(qty_incoming) as qty_incoming')
            ->whereNotNull('ean')
            ->groupBy('ean')
            ->pluck('qty_incoming', 'ean');
        $fddIncomingCount = ProductStatIncomingItem::query()
            ->where('supplier_name', 'Fdd')
            ->count();
        $fddIncomingUnits = (int) ProductStatIncomingItem::query()
            ->where('supplier_name', 'Fdd')
            ->sum('qty_incoming');
        $supplierStocks = ProductStatSupplierStock::query()
            ->get()
            ->groupBy(fn (ProductStatSupplierStock $stock) => mb_strtolower($stock->supplier_name))
            ->map(fn ($stocks) => $stocks->keyBy('supplier_code'));
        $hurtelStocks = $supplierStocks->get('hurtel', collect());

        $items = ProductStatItem::query()
            ->when($latestImport, fn ($query) => $query->where('import_id', $latestImport->id))
            ->when($supplierFilter !== '', function ($query) use ($supplierFilter) {
                $query->where(function ($supplierQuery) use ($supplierFilter) {
                    $supplierQuery
                        ->where('supplier_name', $supplierFilter)
                        ->orWhere('supplier_raw', 'like', '%'.$supplierFilter.'%');
                });
            })
            ->when($search !== '', function ($query) use ($search) {
                $search = '%'.$search.'%';

                $query->where(function ($searchQuery) use ($search) {
                    $searchQuery
                        ->where('product_name', 'like', $search)
                        ->orWhere('sku', 'like', $search)
                        ->orWhere('ean', 'like', $search);
                });
            })
            ->when($stockField !== '' && $stockOperator !== '' && $stockValue !== null, function ($query) use ($stockField, $stockOperator, $stockValue) {
                if (! in_array($stockField, ['s1', 's2'], true) || ! in_array($stockOperator, ['<', '<=', '=', '>=', '>'], true)) {
                    return;
                }

                $query->where($stockField === 's2' ? 'stock_s2' : 'stock_s1', $stockOperator, $stockValue);
            })
            ->orderBy('supplier_name')
            ->orderByDesc('sales_p30')
            ->orderBy('product_name')
            ->get();
        $periodDays = $latestImport?->period_from && $latestImport?->period_to
            ? max(1, $latestImport->period_from->diffInDays($latestImport->period_to) + 1)
            : null;

        $enrichedItems = $items->map(function (ProductStatItem $item) use ($incomingByEan, $periodDays) {
            $incomingQty = (int) ($incomingByEan[$item->ean] ?? 0);
            $targetCoverageDays = 45;
            $dailySales = $item->sales_p30 > 0 ? round($item->sales_p30 / 30, 2) : 0.0;
            $trendT30 = $periodDays ? (int) round(($item->sales_period / $periodDays) * 30) : null;
            $effectiveS1 = $item->stock_s1 + $incomingQty;
            $s1CoverageDays = $dailySales > 0 ? round($effectiveS1 / $dailySales, 1) : null;
            $totalCoverageDays = $dailySales > 0 ? round(($effectiveS1 + $item->stock_s2) / $dailySales, 1) : null;
            $alzaReadyStock = $item->stock_s1 + $item->stock_s2;
            $recommendedS1 = filled($item->clearance_flag) ? 0 : max(0, (int) ceil(($dailySales * $targetCoverageDays) - $effectiveS1));

            $status = $this->resolveStatus(
                clearanceFlag: $item->clearance_flag,
                salesP30: $item->sales_p30,
                salesPop: $item->sales_pop,
                stockS1: $effectiveS1,
                s1CoverageDays: $s1CoverageDays,
            );

            $item->incoming_qty = $incomingQty;
            $item->effective_stock_s1 = $effectiveS1;
            $item->daily_sales = $dailySales;
            $item->trend_t30 = $trendT30;
            $item->s1_coverage_days = $s1CoverageDays;
            $item->total_coverage_days = $totalCoverageDays;
            $item->alza_ready_stock = $alzaReadyStock;
            $item->recommended_s1_order = $recommendedS1;
            $item->status_meta = $status;
            $item->supplier_names = $this->extractSupplierNames($item->supplier_raw);

            return $item;
        });

        $filteredItems = $enrichedItems
            ->when($supplierFilter !== '', fn ($collection) => $collection->filter(fn ($item) => in_array($supplierFilter, $item->supplier_names, true)))
            ->when($search !== '', fn ($collection) => $collection->filter(function ($item) use ($search) {
                return str_contains(mb_strtolower($item->product_name), mb_strtolower($search))
                    || str_contains(mb_strtolower($item->sku), mb_strtolower($search))
                    || str_contains(mb_strtolower((string) $item->ean), mb_strtolower($search));
            }))
            ->when($statusFilter !== 'all', fn ($collection) => $collection->filter(fn ($item) => $item->status_meta['key'] === $statusFilter))
            ->when($stockField !== '' && $stockOperator !== '' && $stockValue !== null, function ($collection) use ($stockField, $stockOperator, $stockValue) {
                return $collection->filter(function ($item) use ($stockField, $stockOperator, $stockValue) {
                    $value = $stockField === 's2' ? $item->stock_s2 : $item->stock_s1;

                    return match ($stockOperator) {
                        '<' => $value < $stockValue,
                        '<=' => $value <= $stockValue,
                        '=' => $value === $stockValue,
                        '>=' => $value >= $stockValue,
                        '>' => $value > $stockValue,
                        default => true,
                    };
                });
            })
            ->values();

        $priorityOrder = ['order' => 0, 'lost_sales' => 1, 'watch' => 2, 'price_check' => 3, 'stock' => 4, 'clearance' => 5];

        $filteredItems = $filteredItems->sortBy([
            fn ($a, $b) => ($b->last_order_at?->timestamp ?? 0) <=> ($a->last_order_at?->timestamp ?? 0),
            fn ($a, $b) => ($priorityOrder[$a->status_meta['key']] ?? 99) <=> ($priorityOrder[$b->status_meta['key']] ?? 99),
            fn ($a, $b) => $b->recommended_s1_order <=> $a->recommended_s1_order,
            fn ($a, $b) => $b->sales_p30 <=> $a->sales_p30,
            fn ($a, $b) => strcmp((string) $a->product_name, (string) $b->product_name),
        ])->values();

        $supplierEntries = $filteredItems
            ->flatMap(function ($item) use ($supplierSettings, $supplierStocks) {
                return collect($item->supplier_names)->map(function ($supplier) use ($item, $supplierSettings, $supplierStocks) {
                    $groupItem = clone $item;
                    $supplierSetting = $supplierSettings->get(mb_strtolower($supplier));
                    $targetCoverageDays = (int) ($supplierSetting?->target_coverage_days ?: 45);
                    $supplierEan = $this->extractSupplierCodeForSupplier($item->supplier_raw, $supplier, $item->stock_code, $item->ean);
                    $supplierMatchCode = $item->supplier_code ?: $supplierEan;

                    $groupItem->current_supplier_name = $supplier;
                    $groupItem->current_supplier_stock_code = $supplierEan;
                    $supplierStock = $supplierStocks->get(mb_strtolower($supplier));
                    $groupItem->current_supplier_stock_status = $supplierStock
                        ? ($supplierStock->get((string) $supplierMatchCode)?->stock_status ?? 'unknown')
                        : null;
                    $groupItem->current_supplier_portal_url = $this->buildSupplierPortalUrl($supplierSetting?->portal_url_template, $supplierEan ?: $item->ean);
                    $groupItem->current_supplier_badge = mb_strtoupper(mb_substr($supplier, 0, 2));
                    $groupItem->target_coverage_days = $targetCoverageDays;
                    $groupItem->recommended_s1_order = filled($groupItem->clearance_flag)
                        ? 0
                        : max(0, (int) ceil(($groupItem->daily_sales * $targetCoverageDays) - $groupItem->effective_stock_s1));

                    return [
                        'supplier' => $supplier,
                        'item' => $groupItem,
                    ];
                });
            });

        if ($supplierStockFilter !== 'all') {
            $supplierEntries = $supplierEntries
                ->filter(fn ($entry) => $entry['item']->current_supplier_stock_status === $supplierStockFilter)
                ->values();
        }

        $supplierGroups = $supplierEntries
            ->groupBy('supplier')
            ->map(function ($group, $supplier) use ($priorityOnly, $supplierFilter) {
                $allItems = $group->pluck('item')->values();

                if ($priorityOnly || $supplierFilter !== '') {
                    $purchaseOrder = ['order' => 0, 'lost_sales' => 1, 'watch' => 2, 'price_check' => 3, 'stock' => 4, 'clearance' => 5];

                    $allItems = $allItems->sortBy([
                        fn ($a, $b) => ($b->last_order_at?->timestamp ?? 0) <=> ($a->last_order_at?->timestamp ?? 0),
                        fn ($a, $b) => ($purchaseOrder[$a->status_meta['key']] ?? 99) <=> ($purchaseOrder[$b->status_meta['key']] ?? 99),
                        fn ($a, $b) => $b->recommended_s1_order <=> $a->recommended_s1_order,
                        fn ($a, $b) => ($a->stock_s1 > 0 ? 1 : 0) <=> ($b->stock_s1 > 0 ? 1 : 0),
                        fn ($a, $b) => $b->sales_p30 <=> $a->sales_p30,
                        fn ($a, $b) => strcmp((string) $a->product_name, (string) $b->product_name),
                    ])->when($priorityOnly, fn ($collection) => $collection->take(20))->values();
                }

                return [
                    'supplier' => $supplier,
                    'items' => $allItems,
                    'count' => $allItems->count(),
                    'sales_p30' => $allItems->sum('sales_p30'),
                    'stock_s1' => $allItems->sum('stock_s1'),
                    'incoming_qty' => $allItems->sum('incoming_qty'),
                    'stock_s2' => $allItems->sum('stock_s2'),
                    'to_order_count' => $allItems->filter(fn ($item) => $item->status_meta['key'] === 'order')->count(),
                    'to_order_units' => $allItems->sum('recommended_s1_order'),
                ];
            })
            ->values();

        $prioritySuppliers = $supplierEntries
            ->groupBy('supplier')
            ->map(function ($group, $supplier) {
                $items = $group->pluck('item')->values();
                $criticalItems = $items->filter(fn ($item) => $item->status_meta['key'] === 'order')->values();
                $riskItems = $items->filter(fn ($item) => $item->stock_s1 <= 0 && $item->sales_p30 > 0)->values();
                $urgencyScore = $items->sum(function ($item) {
                    if ($item->status_meta['key'] === 'clearance') {
                        return 0;
                    }

                    $score = 0;

                    if ($item->status_meta['key'] === 'order') {
                        $score += 100;
                    } elseif ($item->status_meta['key'] === 'watch') {
                        $score += 40;
                    }

                    if ($item->stock_s1 <= 0 && $item->sales_p30 > 0) {
                        $score += 80;
                    }

                    $score += min(60, $item->sales_p30 * 2);
                    $score += min(50, $item->recommended_s1_order);

                    return $score;
                });

                return [
                    'supplier' => $supplier,
                    'critical_count' => $criticalItems->count(),
                    'risk_count' => $riskItems->count(),
                    'recommended_units' => $criticalItems->sum('recommended_s1_order'),
                    'sales_p30' => $items->sum('sales_p30'),
                    'min_s1_days' => $criticalItems->min(fn ($item) => $item->s1_coverage_days ?? 9999),
                    'top_items' => $criticalItems
                        ->sortByDesc(fn ($item) => ($item->stock_s1 <= 0 ? 1000 : 0) + ($item->sales_p30 * 10) + $item->recommended_s1_order)
                        ->take(10)
                        ->map(fn ($item) => [
                            'name' => $item->product_name,
                            'sku' => $item->sku,
                            'ean' => $item->ean,
                            'mobdata_url' => 'https://admin.mobdata.eu/produkty/sklad/index/hladat/'.urlencode((string) $item->sku).'/hladaj_typ/kod',
                            'supplier_portal_url' => $item->current_supplier_portal_url,
                            'supplier_badge' => $item->current_supplier_badge,
                            'recommended' => $item->recommended_s1_order,
                            'sales_p30' => $item->sales_p30,
                            'stock_s1' => $item->stock_s1,
                            'incoming_qty' => $item->incoming_qty,
                            'last_order_at' => $item->last_order_at?->format('d.m.Y'),
                        ])
                        ->values(),
                    'urgency_score' => $urgencyScore,
                ];
            })
            ->filter(fn ($group) => $group['critical_count'] > 0 || $group['risk_count'] > 0)
            ->sortByDesc('urgency_score')
            ->values();

        return [
            'latestImport' => $latestImport,
            'supplierGroups' => $supplierGroups,
            'prioritySuppliers' => $prioritySuppliers,
            'stats' => [
                'products' => $filteredItems->count(),
                'suppliers' => $supplierGroups->count(),
                'sales_p30' => $filteredItems->sum('sales_p30'),
                'clearance' => $filteredItems->filter(fn ($item) => filled($item->clearance_flag))->count(),
                'priority_suppliers' => $prioritySuppliers->count(),
                'priority_products' => $filteredItems->filter(fn ($item) => $item->status_meta['key'] === 'order')->count(),
                'priority_units' => $filteredItems->filter(fn ($item) => $item->status_meta['key'] === 'order')->sum('recommended_s1_order'),
            ],
            'supplierOptions' => $enrichedItems->flatMap(fn ($item) => $item->supplier_names)->filter()->unique()->sort()->values(),
            'supplierFilter' => $supplierFilter,
            'supplierStockFilter' => $supplierStockFilter,
            'statusFilter' => $statusFilter,
            'search' => $search,
            'stockField' => $stockField,
            'stockOperator' => $stockOperator,
            'stockValue' => $stockValue,
            'priorityOnly' => $priorityOnly,
            'fddIncomingCount' => $fddIncomingCount,
            'hurtelStockCount' => $hurtelStocks->count(),
            'hurtelStockCheckedAt' => $hurtelStocks->max('checked_at'),
            'fddIncomingUnits' => $fddIncomingUnits,
        ];
    }

    private function authorizeAccess(Request $request): void
    {
        abort_unless($request->user()?->canManageUsers(), 403);
    }

    private function buildPreparationPayload(array $filters = []): array
    {
        $latestImport = ProductStatImport::query()->latest()->first();
        $lastSalesAt = SalesOrderLine::query()->max('ordered_at');
        $supplierFilter = trim((string) ($filters['supplier'] ?? ''));

        if (! $latestImport || ! $lastSalesAt) {
            return [
                'latestImport' => $latestImport,
                'lastSalesAt' => $lastSalesAt,
                'rows' => collect(),
                'supplierOptions' => collect(),
                'supplierFilter' => $supplierFilter,
            ];
        }

        $items = ProductStatItem::query()
            ->where('import_id', $latestImport->id)
            ->get();
        $skuList = $items->pluck('sku')->filter()->unique()->values();

        if ($skuList->isEmpty()) {
            return [
                'latestImport' => $latestImport,
                'lastSalesAt' => $lastSalesAt,
                'rows' => collect(),
                'supplierOptions' => collect(),
                'supplierFilter' => $supplierFilter,
            ];
        }

        $anchor = CarbonImmutable::parse($lastSalesAt)->endOfDay();
        $start90 = $anchor->subDays(89)->startOfDay();
        $start180 = $anchor->subDays(179)->startOfDay();
        $q4Start = CarbonImmutable::create($anchor->year - 1, 10, 1)->startOfDay();
        $q4End = CarbonImmutable::create($anchor->year - 1, 12, 31)->endOfDay();
        $incomingByEan = ProductStatIncomingItem::query()
            ->selectRaw('ean, SUM(qty_incoming) as qty_incoming')
            ->whereNotNull('ean')
            ->groupBy('ean')
            ->pluck('qty_incoming', 'ean');
        $supplierSettings = ProductStatSupplierSetting::query()
            ->get()
            ->keyBy(fn (ProductStatSupplierSetting $setting) => mb_strtolower($setting->supplier_name));

        $salesBySku = $skuList
            ->chunk(800)
            ->flatMap(fn ($chunk) => SalesOrderLine::query()
                ->select('model')
                ->selectRaw("SUM(CASE WHEN ordered_at >= ? AND channel_type = 'own_web' THEN quantity ELSE 0 END) as own_web_90", [$start90->toDateTimeString()])
                ->selectRaw("SUM(CASE WHEN ordered_at >= ? AND channel_type = 'marketplace' THEN quantity ELSE 0 END) as marketplace_90", [$start90->toDateTimeString()])
                ->selectRaw("SUM(CASE WHEN ordered_at >= ? AND channel_type = 'marketplace' AND UPPER(COALESCE(marketplace_display, '')) LIKE 'ALZA%' THEN quantity ELSE 0 END) as alza_90", [$start90->toDateTimeString()])
                ->selectRaw("SUM(CASE WHEN ordered_at >= ? AND channel_type = 'own_web' THEN quantity ELSE 0 END) as own_web_180", [$start180->toDateTimeString()])
                ->selectRaw("SUM(CASE WHEN ordered_at >= ? AND channel_type = 'marketplace' THEN quantity ELSE 0 END) as marketplace_180", [$start180->toDateTimeString()])
                ->selectRaw("SUM(CASE WHEN ordered_at >= ? AND channel_type = 'marketplace' AND UPPER(COALESCE(marketplace_display, '')) LIKE 'ALZA%' THEN quantity ELSE 0 END) as alza_180", [$start180->toDateTimeString()])
                ->selectRaw("SUM(CASE WHEN ordered_at >= ? AND ordered_at <= ? AND channel_type = 'own_web' THEN quantity ELSE 0 END) as own_web_q4", [$q4Start->toDateTimeString(), $q4End->toDateTimeString()])
                ->selectRaw("SUM(CASE WHEN ordered_at >= ? AND ordered_at <= ? AND channel_type = 'marketplace' THEN quantity ELSE 0 END) as marketplace_q4", [$q4Start->toDateTimeString(), $q4End->toDateTimeString()])
                ->selectRaw("SUM(CASE WHEN ordered_at >= ? AND ordered_at <= ? AND channel_type = 'marketplace' AND UPPER(COALESCE(marketplace_display, '')) LIKE 'ALZA%' THEN quantity ELSE 0 END) as alza_q4", [$q4Start->toDateTimeString(), $q4End->toDateTimeString()])
                ->selectRaw('MAX(ordered_at) as sales_last_at')
                ->whereIn('model', $chunk->all())
                ->groupBy('model')
                ->get())
            ->keyBy('model');

        $supplierOptions = $items
            ->flatMap(fn (ProductStatItem $item) => $this->extractSupplierNames($item->supplier_raw))
            ->filter()
            ->unique()
            ->sort()
            ->values();
        $limit = $supplierFilter !== '' ? 200 : 30;

        $rows = $items
            ->map(function (ProductStatItem $item) use ($incomingByEan, $salesBySku, $supplierSettings) {
                $sales = $salesBySku->get($item->sku);
                $sales90S2 = (float) ($sales?->alza_90 ?? 0);
                $sales90S1 = max(0, (float) ($sales?->own_web_90 ?? 0) + (float) ($sales?->marketplace_90 ?? 0) - $sales90S2);
                $sales180S2 = (float) ($sales?->alza_180 ?? 0);
                $sales180S1 = max(0, (float) ($sales?->own_web_180 ?? 0) + (float) ($sales?->marketplace_180 ?? 0) - $sales180S2);
                $salesQ4S2 = (float) ($sales?->alza_q4 ?? 0);
                $salesQ4S1 = max(0, (float) ($sales?->own_web_q4 ?? 0) + (float) ($sales?->marketplace_q4 ?? 0) - $salesQ4S2);
                $sales90 = $sales90S1 + $sales90S2;
                $sales180 = $sales180S1 + $sales180S2;
                $salesQ4 = $salesQ4S1 + $salesQ4S2;
                $incomingQty = (int) ($incomingByEan[$item->ean] ?? 0);
                $ageDays = (int) ($item->product_age_days ?? 0);
                $q4ForecastS1 = $ageDays >= 365 ? (int) ceil($salesQ4S1 * 1.15) : 0;
                $q4ForecastS2 = $ageDays >= 365 ? (int) ceil($salesQ4S2 * 1.15) : 0;
                $forecastS1 = max((int) ceil(($sales90S1 / 90) * 120), $q4ForecastS1);
                $forecastS2 = max((int) ceil(($sales90S2 / 90) * 120), $q4ForecastS2);
                $recommendedS1 = max(0, $forecastS1 - (int) $item->stock_s1 - $incomingQty);
                $recommendedS2 = max(0, $forecastS2 - (int) $item->stock_s2);
                $forecast = $forecastS1 + $forecastS2;
                $recommended = $recommendedS1 + $recommendedS2;
                $supplier = $this->extractSupplierNames($item->supplier_raw)[0] ?? '---';
                $supplierSetting = $supplierSettings->get(mb_strtolower($supplier));
                $supplierCode = $this->extractSupplierCodeForSupplier($item->supplier_raw, $supplier, $item->stock_code, $item->ean);
                $score = $recommended * 3 + min(120, (int) $sales90) + min(160, (int) $salesQ4);

                if ($item->stock_s1 <= 0) {
                    $score += 80;
                }
                if ($item->stock_s2 <= 0 && $sales90S2 > 0) {
                    $score += 80;
                }

                return [
                    'product_name' => $item->product_name,
                    'sku' => $item->sku,
                    'supplier' => $supplier,
                    'supplier_badge' => mb_strtoupper(mb_substr($supplier, 0, 2)),
                    'supplier_url' => $this->buildSupplierPortalUrl($supplierSetting?->portal_url_template, $supplierCode ?: $item->ean),
                    'stock_s1' => (int) $item->stock_s1,
                    'stock_s2' => (int) $item->stock_s2,
                    'incoming_qty' => $incomingQty,
                    'sales_90' => (int) round($sales90),
                    'sales_90_s1' => (int) round($sales90S1),
                    'sales_90_s2' => (int) round($sales90S2),
                    'sales_180' => (int) round($sales180),
                    'sales_180_s1' => (int) round($sales180S1),
                    'sales_180_s2' => (int) round($sales180S2),
                    'sales_q4' => (int) round($salesQ4),
                    'sales_q4_s1' => (int) round($salesQ4S1),
                    'sales_q4_s2' => (int) round($salesQ4S2),
                    'product_age_days' => $ageDays,
                    'forecast' => $forecast,
                    'recommended' => $recommended,
                    'forecast_s1' => $forecastS1,
                    'forecast_s2' => $forecastS2,
                    'recommended_s1' => $recommendedS1,
                    'recommended_s2' => $recommendedS2,
                    'last_order_at' => $sales?->sales_last_at ? CarbonImmutable::parse($sales->sales_last_at)->format('d.m.Y') : null,
                    'reason' => $salesQ4S2 > 0 ? 'Alza Q4 '.$this->formatQty($salesQ4S2).' ks' : ($q4ForecastS1 > 0 ? 'Q4 S1 '.$this->formatQty($salesQ4S1).' ks' : 'S90 '.$this->formatQty($sales90).' ks'),
                    'score' => $score,
                ];
            })
            ->when($supplierFilter !== '', fn ($collection) => $collection->filter(fn (array $row) => $row['supplier'] === $supplierFilter))
            ->filter(fn (array $row) => $row['recommended'] > 0 && ($row['sales_90'] > 0 || $row['sales_q4'] > 0))
            ->sortByDesc('score')
            ->take($limit)
            ->values();

        return [
            'latestImport' => $latestImport,
            'lastSalesAt' => CarbonImmutable::parse($lastSalesAt)->format('d.m.Y'),
            'rows' => $rows,
            'supplierOptions' => $supplierOptions,
            'supplierFilter' => $supplierFilter,
        ];
    }

    private function formatQty(float $value): string
    {
        return number_format($value, 0, ',', ' ');
    }

    private function resolveStatus(?string $clearanceFlag, int $salesP30, int $salesPop, int $stockS1, ?float $s1CoverageDays): array
    {
        if (filled($clearanceFlag)) {
            return ['key' => 'clearance', 'label' => 'Výpredaj', 'class' => 'sl-status-blocked'];
        }

        if ($salesP30 <= 0 && $salesPop > 0 && $stockS1 <= 0) {
            return ['key' => 'lost_sales', 'label' => 'Dopyt', 'class' => 'sl-status-blocked'];
        }

        if ($salesP30 <= 0 && $salesPop > 0 && $stockS1 > 0) {
            return ['key' => 'price_check', 'label' => 'Cena', 'class' => 'sl-status-in_progress'];
        }

        if ($salesP30 <= 0) {
            return ['key' => 'price_check', 'label' => 'Cena', 'class' => 'sl-status-in_progress'];
        }

        if ($stockS1 <= 0 || $stockS1 <= $salesP30 || ($s1CoverageDays !== null && $s1CoverageDays <= 30)) {
            return ['key' => 'order', 'label' => 'Objednať', 'class' => 'sl-status-blocked'];
        }

        if ($s1CoverageDays !== null && $s1CoverageDays <= 21) {
            return ['key' => 'watch', 'label' => 'Sledovať', 'class' => 'sl-status-in_progress'];
        }

        return ['key' => 'stock', 'label' => 'Dostatok', 'class' => 'sl-status-new'];
    }

    private function buildSupplierPortalUrl(?string $template, ?string $code): ?string
    {
        if (blank($template) || blank($code)) {
            return null;
        }

        return str_replace(['{code}', '{EAN}', '{ean}'], urlencode($code), $template);
    }

    private function extractSupplierNames(?string $supplierRaw): array
    {
        $raw = trim((string) $supplierRaw);

        if ($raw === '') {
            return ['Bez dodávateľa'];
        }

        $names = collect(preg_split('/\s*,\s*/', $raw, -1, PREG_SPLIT_NO_EMPTY))
            ->map(function ($segment) {
                $segment = trim((string) $segment);

                if ($segment === '') {
                    return null;
                }

                preg_match_all('/[\p{L}\p{N}]+/u', $segment, $matches);
                $parts = collect($matches[0] ?? [])
                    ->filter(fn ($part) => preg_match('/\p{L}/u', $part) === 1)
                    ->values()
                    ->all();

                if ($parts === []) {
                    return null;
                }

                return mb_convert_case(mb_strtolower(implode(' ', $parts)), MB_CASE_TITLE, 'UTF-8');
            })
            ->filter()
            ->unique()
            ->values()
            ->all();

        return $names !== [] ? $names : ['Bez dodávateľa'];
    }

    private function extractSupplierCodeForSupplier(?string $supplierRaw, string $supplierName, ?string $stockCode, ?string $ean): ?string
    {
        $supplierKey = mb_strtolower($supplierName);

        foreach (preg_split('/\s*,\s*/', (string) $supplierRaw, -1, PREG_SPLIT_NO_EMPTY) as $segment) {
            if (! str_contains(mb_strtolower((string) $segment), $supplierKey)) {
                continue;
            }

            preg_match('/\d{8,14}/', (string) $segment, $match);
            $code = $match[0] ?? null;

            if ($code && $code !== (string) $stockCode && $code !== (string) $ean) {
                return $code;
            }
        }

        return null;
    }
}
