<x-app-layout>
    <style>
        .product-stats-table-wrap {
            overflow-x: auto;
            overflow-y: visible;
            content-visibility: auto;
            contain-intrinsic-size: 0 900px;
        }

        .product-stats-table th {
            position: sticky;
            top: 0;
            z-index: 20;
            background: #f1f5f9;
            padding-top: .5rem !important;
            padding-bottom: .5rem !important;
            text-align: center;
        }

        .product-stats-table tbody td {
            background: #fff;
            padding-top: .2rem !important;
            padding-bottom: .2rem !important;
            line-height: 1.05 !important;
            height: 2.1rem !important;
            vertical-align: middle;
            text-align: center;
        }

        .product-stats-table tbody tr:hover td {
            background: #f8fafc;
        }

        .product-stats-table th:nth-child(12),
        .product-stats-table td:nth-child(12) {
            background: #dbeafe;
        }

        .product-stats-table th:first-child,
        .product-stats-table td:first-child {
            text-align: left;
        }

        .product-stats-table th:nth-child(2),
        .product-stats-table td:nth-child(2) {
            text-align: left;
        }

        .product-stats-table td:nth-child(2) .leading-tight > div {
            max-width: 84px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .product-stats-table input[name^="quantities"] {
            -moz-appearance: textfield;
            width: 48px !important;
            min-width: 48px !important;
            max-width: 48px !important;
        }

        .product-stats-table input[name^="quantities"]::-webkit-outer-spin-button,
        .product-stats-table input[name^="quantities"]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
    </style>

    <x-slot name="header">
        <div class="mx-auto max-w-[1720px] px-1">
            <section class="sl-card overflow-hidden">
                <div class="px-5 py-4 sm:px-6">
                    <div class="flex flex-col gap-3 xl:flex-row xl:items-center xl:justify-between">
                        <div>
                            <p class="sl-kicker">Admin module</p>
                            <h1 class="mt-1 text-2xl font-semibold tracking-tight text-slate-50">Product Stats</h1>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="sl-pill">Produkty <strong class="text-slate-50">{{ $stats['products'] }}</strong></span>
                            <span class="sl-pill">Dodávatelia <strong class="text-slate-50">{{ $stats['suppliers'] }}</strong></span>
                            <span class="sl-pill">P30 <strong class="text-slate-50">{{ $stats['sales_p30'] }}</strong></span>
                            <span class="sl-pill">Výpredaj <strong class="text-slate-50">{{ $stats['clearance'] }}</strong></span>
                            <a href="{{ route('product-stats.preparation') }}" class="sl-btn-secondary !px-3 !py-2">Príprava</a>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </x-slot>

    <div class="sl-shell mt-3 space-y-3">
        @if (session('status'))
            <div class="flash-banner">{{ session('status') }}</div>
        @endif

        <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <details>
                <summary class="flex cursor-pointer list-none items-center justify-between gap-3 text-left">
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.28em] text-slate-500">Importy</p>
                        <p class="mt-2 text-sm text-slate-600">
                            Zdroj: <span class="text-slate-900">predaj.xlsx</span>
                            @if ($latestImport)
                                , naposledy {{ $latestImport->created_at?->format('d.m.Y H:i') }} / {{ $latestImport->row_count }} riadkov
                                @if ($latestImport->period_from || $latestImport->period_to)
                                    , obdobie
                                    <span class="text-slate-900">
                                        {{ $latestImport->period_from?->format('d.m.Y') ?: '---' }} - {{ $latestImport->period_to?->format('d.m.Y') ?: '---' }}
                                    </span>
                                @endif
                            @endif
                        </p>
                    </div>
                    <span class="sl-btn-secondary !px-3 !py-2">Otvoriť</span>
                </summary>
                <div class="mt-4 flex flex-wrap items-center gap-4">
                    <form method="POST" action="{{ route('product-stats.import') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="flex flex-wrap items-end gap-2">
                            <div>
                                <label class="mb-1 block text-xs font-semibold text-slate-500">Od</label>
                                <input type="date" name="period_from" value="{{ old('period_from', now()->subDays(90)->toDateString()) }}" class="sl-input !h-10 !w-[150px] !bg-white !text-slate-900">
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold text-slate-500">Do</label>
                                <input type="date" name="period_to" value="{{ old('period_to', now()->toDateString()) }}" class="sl-input !h-10 !w-[150px] !bg-white !text-slate-900">
                            </div>
                            <label class="sl-btn-primary cursor-pointer">
                                Sumarizácia XLSX
                                <input type="file" name="file" accept=".xlsx" class="sr-only" required onchange="this.form.submit()">
                            </label>
                        </div>
                    </form>
                </div>
                @error('file')
                    <p class="mt-3 text-sm text-rose-500">{{ $message }}</p>
                @enderror
            </details>
        </section>

        <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <form method="GET" action="{{ route('product-stats.index') }}" class="space-y-3">
                <div style="display:grid;grid-template-columns:minmax(0,1.35fr) minmax(180px,0.82fr) minmax(180px,0.82fr) minmax(150px,0.55fr) minmax(120px,0.45fr) minmax(90px,0.28fr) minmax(90px,0.28fr);gap:12px;align-items:end;">
                    <div class="min-w-0">
                        <label class="sl-form-label" style="color:#0f172a;">Hľadať</label>
                        <input type="text" name="q" value="{{ $search }}" placeholder="Produkt, SKU, EAN" class="sl-input">
                    </div>
                    <div>
                        <label class="sl-form-label" style="color:#0f172a;">Dodávateľ</label>
                        <select name="supplier" class="sl-input !bg-white !text-slate-900">
                            <option value="">Všetci dodávatelia</option>
                            @foreach ($supplierOptions as $supplier)
                                <option value="{{ $supplier }}" @selected($supplierFilter === $supplier)>{{ $supplier }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="sl-form-label" style="color:#0f172a;">Stav</label>
                        <select name="status" class="sl-input">
                            <option value="all" @selected($statusFilter === 'all')>Všetky stavy</option>
                            <option value="order" @selected($statusFilter === 'order')>Objednať</option>
                            <option value="lost_sales" @selected($statusFilter === 'lost_sales')>Dopyt</option>
                            <option value="watch" @selected($statusFilter === 'watch')>Sledovať</option>
                            <option value="price_check" @selected($statusFilter === 'price_check')>Cena</option>
                            <option value="stock" @selected($statusFilter === 'stock')>Dostatok</option>
                            <option value="clearance" @selected($statusFilter === 'clearance')>Výpredaj</option>
                        </select>
                    </div>
                    <div>
                        <label class="sl-form-label" style="color:#0f172a;">DOD sklad</label>
                        <select name="supplier_stock" class="sl-input !bg-white !text-slate-900">
                            <option value="all" @selected($supplierStockFilter === 'all')>Všetky</option>
                            <option value="in_stock" @selected($supplierStockFilter === 'in_stock')>Skladom</option>
                            <option value="out_of_stock" @selected($supplierStockFilter === 'out_of_stock')>Vypredané</option>
                            <option value="unknown" @selected($supplierStockFilter === 'unknown')>Neznáme</option>
                        </select>
                    </div>
                    <div>
                        <label class="sl-form-label" style="color:#0f172a;">Sklad</label>
                        <select name="stock_field" class="sl-input !bg-white !text-slate-900">
                            <option value="">Bez filtra</option>
                            <option value="s1" @selected($stockField === 's1')>S1</option>
                            <option value="s2" @selected($stockField === 's2')>S2</option>
                        </select>
                    </div>
                    <div>
                        <label class="sl-form-label" style="color:#0f172a;">Op.</label>
                        <select name="stock_operator" class="sl-input !bg-white !text-slate-900">
                            <option value="">-</option>
                            <option value="<" @selected($stockOperator === '<')>&lt;</option>
                            <option value="<=" @selected($stockOperator === '<=')>&lt;=</option>
                            <option value="=" @selected($stockOperator === '=')>=</option>
                            <option value=">=" @selected($stockOperator === '>=')>&gt;=</option>
                            <option value=">" @selected($stockOperator === '>')>&gt;</option>
                        </select>
                    </div>
                    <div>
                        <label class="sl-form-label" style="color:#0f172a;">Hodn.</label>
                        <input type="number" min="0" name="stock_value" value="{{ $stockValue ?? '' }}" class="sl-input !bg-white !text-slate-900">
                    </div>
                </div>
                <div class="flex flex-wrap gap-2">
                    <label class="inline-flex items-center gap-2 rounded-xl bg-slate-100 px-3 py-2 text-sm text-slate-700">
                        <input type="checkbox" name="priority" value="1" @checked($priorityOnly)>
                        <span>TOP 20</span>
                    </label>
                    <button type="submit" class="sl-btn-primary">Filtrovať</button>
                    <a href="{{ route('product-stats.index') }}" class="sl-btn-secondary">Reset</a>
                    <button type="button" data-product-stats-continue class="sl-btn-secondary hidden">Pokračovať</button>
                </div>
            </form>
        </section>

        @if (false && $priorityOnly && $prioritySuppliers->isNotEmpty())
            <section class="space-y-4">
                <section class="rounded-3xl border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-200 px-5 py-4">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.28em] text-slate-500">Priority dodávatelia</p>
                        <h2 class="mt-2 text-xl font-semibold text-slate-900">Prioritné</h2>
                    </div>
                    <div class="divide-y divide-slate-200">
                        @foreach ($prioritySuppliers as $index => $supplier)
                            <article class="px-5 py-4">
                                <div class="flex flex-col gap-4">
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-3">
                                            <span class="inline-flex h-8 min-w-8 items-center justify-center rounded-full bg-rose-600 px-2 text-xs font-bold text-white">{{ $index + 1 }}</span>
                                            <div class="flex flex-wrap items-center gap-3">
                                                <h3 class="text-lg font-semibold text-slate-900">{{ $supplier['supplier'] }}</h3>
                                                <p class="text-sm text-slate-500">
                                                    {{ $supplier['critical_count'] }} produktov
                                                    · Bez skladu {{ $supplier['risk_count'] }}
                                                    · P30 {{ $supplier['sales_p30'] }}
                                                </p>
                                            </div>
                                        </div>
                                        @if ($supplier['top_items']->isNotEmpty())
                                            <div class="mt-4 overflow-x-auto">
                                                <table class="min-w-[870px] w-full table-fixed text-sm">
                                                    <colgroup>
                                                        <col style="width: 35%;">
                                                        <col style="width: 14%;">
                                                        <col style="width: 16%;">
                                                        <col style="width: 8%;">
                                                        <col style="width: 8%;">
                                                        <col style="width: 9%;">
                                                        <col style="width: 10%;">
                                                    </colgroup>
                                                    <thead>
                                                        <tr class="border-b border-slate-200 text-left text-sm font-semibold text-slate-900">
                                                            <th class="px-4 py-2">Produkt</th>
                                                            <th class="px-3 py-2">SKU</th>
                                                            <th class="px-3 py-2">EAN</th>
                                                            <th class="px-3 py-2">S1</th>
                                                            <th class="px-3 py-2 text-blue-600">P30</th>
                                                            <th class="px-3 py-2">Objednať</th>
                                                            <th class="px-3 py-2">LastOrder</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($supplier['top_items'] as $item)
                                                            <tr class="border-t border-slate-200 bg-slate-50 text-sm text-slate-900">
                                                                <td class="px-4 py-3">
                                                                    <div class="flex items-center gap-2 min-w-0">
                                                                        <a href="{{ $item['mobdata_url'] }}" target="_blank" rel="noreferrer" class="block min-w-0 flex-1 overflow-hidden text-ellipsis whitespace-nowrap font-semibold text-slate-900 hover:text-rose-500" title="{{ $item['name'] }}">{{ $item['name'] }}</a>
                                                                        @if (!empty($item['supplier_portal_url']))
                                                                            <a href="{{ $item['supplier_portal_url'] }}" target="_blank" rel="noreferrer" class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-md bg-slate-100 text-[11px] font-bold text-slate-600 hover:bg-slate-200 hover:text-slate-900" title="Dodávateľ">
                                                                                {{ $item['supplier_badge'] }}
                                                                            </a>
                                                                        @endif
                                                                    </div>
                                                                </td>
                                                                <td class="whitespace-nowrap px-3 py-3">{{ $item['sku'] }}</td>
                                                                <td class="px-3 py-3">{{ $item['ean'] ?? '---' }}</td>
                                                                <td class="px-3 py-3">{{ $item['stock_s1'] }}</td>
                                                                <td class="px-3 py-3 text-blue-600">{{ $item['sales_p30'] }}</td>
                                                                <td class="px-3 py-3 font-semibold text-rose-600">{{ $item['recommended'] }}</td>
                                                                <td class="px-3 py-3">{{ $item['last_order_at'] ?? '---' }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex">
                                        <a href="{{ route('product-stats.index', array_merge(request()->query(), ['supplier' => $supplier['supplier']])) }}" class="sl-btn-secondary !px-3 !py-2">Otvoriť</a>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>
            </section>
        @endif

        <section class="space-y-4">
            @forelse ($supplierGroups as $group)
                @php
                    $isSelectedSupplier = $supplierFilter !== '' && $supplierFilter === $group['supplier'];
                @endphp
                <article class="overflow-visible rounded-3xl border border-slate-200 bg-white shadow-sm" style="min-width:1464px;">
                    <div class="flex items-center justify-between gap-3 border-b border-slate-200 px-5 py-3">
                        <div class="flex flex-wrap items-center gap-3">
                            <p class="text-lg font-semibold" style="color:#0f172a;">{{ $group['supplier'] }}</p>
                            <p class="text-sm" style="color:#64748b;">{{ $group['count'] }} produktov</p>
                            @if ($isSelectedSupplier)
                                <button type="button" class="sl-btn-secondary !px-3 !py-2" onclick="this.closest('article').querySelectorAll('[data-recommended-qty]').forEach((input)=>input.value=input.dataset.recommendedQty)">Naplniť odporúčanie</button>
                                <button type="submit" form="supplier-export-{{ \Illuminate\Support\Str::slug($group['supplier']) }}" class="sl-btn-primary !px-3 !py-2">Export CSV</button>
                            @endif
                        </div>
                        <div class="flex flex-wrap items-center gap-3 text-sm">
                            <span class="inline-flex items-center rounded-full bg-slate-100 px-4 py-2 text-slate-600">S1 <strong class="ml-2 text-slate-900">{{ $group['stock_s1'] }}</strong></span>
                            <span class="inline-flex items-center rounded-full bg-slate-100 px-4 py-2 text-slate-600" title="Na ceste">✈ <strong class="ml-2 text-slate-900">{{ $group['incoming_qty'] }}</strong></span>
                            <span class="inline-flex items-center rounded-full bg-slate-100 px-4 py-2 text-slate-600">S2 <strong class="ml-2 text-slate-900">{{ $group['stock_s2'] }}</strong></span>
                            <span class="inline-flex items-center rounded-full bg-slate-100 px-4 py-2 text-slate-600">P30 <strong class="ml-2 text-slate-900">{{ $group['sales_p30'] }}</strong></span>
                            <span class="inline-flex items-center rounded-full bg-slate-100 px-4 py-2 text-slate-600">Objednať <strong class="ml-2 text-slate-900">{{ $group['to_order_count'] }}</strong></span>
                        </div>
                    </div>
                    @if ($isSelectedSupplier)
                        <form method="POST" action="{{ route('product-stats.export-csv') }}" class="py-1" id="supplier-export-{{ \Illuminate\Support\Str::slug($group['supplier']) }}">
                            @csrf
                            <input type="hidden" name="supplier" value="{{ $supplierFilter }}">
                            <input type="hidden" name="q" value="{{ $search }}">
                            <input type="hidden" name="status" value="{{ $statusFilter }}">
                            <input type="hidden" name="stock_field" value="{{ $stockField }}">
                            <input type="hidden" name="stock_operator" value="{{ $stockOperator }}">
                            <input type="hidden" name="stock_value" value="{{ $stockValue }}">
                    @endif
                    <div class="product-stats-table-wrap">
                        <table class="product-stats-table table-fixed text-sm" style="min-width:1556px;">
                            <colgroup>
                                <col style="width: 520px;">
                                <col style="width: 110px;">
                                <col style="width: 110px;">
                                <col style="width: 92px;">
                                <col style="width: 92px;">
                                <col style="width: 40px;">
                                <col style="width: 40px;">
                                <col style="width: 40px;">
                                <col style="width: 56px;">
                                <col style="width: 44px;">
                                <col style="width: 44px;">
                                <col style="width: 44px;">
                                <col style="width: 44px;">
                                <col style="width: 52px;">
                                <col style="width: 68px;">
                                <col style="width: 98px;">
                                <col style="width: 64px;">
                            </colgroup>
                            <thead class="bg-slate-100 text-center text-xs uppercase tracking-[0.14em] text-slate-900">
                                <tr>
                                    <th class="px-4 py-3 text-left" style="width:520px;min-width:520px;max-width:520px;">Produkt</th>
                                    <th class="px-3 py-3" style="width:110px;">SKU</th>
                                    <th class="px-3 py-3" style="width:110px;">Sklad. kód</th>
                                    <th class="px-2 py-3" style="width:92px;">DOD sklad</th>
                                    <th class="px-2 py-3" style="width:92px;">Stav</th>
                                    <th class="px-0.5 py-3" style="width:40px;">S1</th>
                                    <th class="px-0.5 py-3 text-center" style="width:40px;" title="Na ceste">✈</th>
                                    <th class="px-0.5 py-3" style="width:40px;">S2</th>
                                    <th class="px-1 py-3" style="width:56px;background:#e11d48;color:#fff;">OBJ</th>
                                    <th class="px-0.5 py-3 text-emerald-600" style="width:44px;">Pre</th>
                                    <th class="px-0.5 py-3 text-teal-600" style="width:44px;">T30</th>
                                    <th class="px-0.5 py-3 text-blue-600" style="width:44px;">P30</th>
                                    <th class="px-0.5 py-3 text-red-600" style="width:44px;">POP</th>
                                    <th class="px-1 py-3" style="width:52px;">S1 dni</th>
                                    <th class="px-2 py-3" style="width:68px;">NC</th>
                                    <th class="px-3 py-3" style="width:98px;">ORDER</th>
                                    <th class="px-2 py-3" style="width:64px;">Dni</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($group['items'] as $item)
                                    <tr class="border-t border-slate-200 text-slate-900 transition-colors"
                                        data-product-stats-row="{{ $group['supplier'] }}|{{ $item->sku }}"
                                    >
                                        <td class="px-4 py-3" style="width:520px;min-width:520px;max-width:520px;">
                                            <div class="flex items-center gap-2 min-w-0">
                                                <a href="https://admin.mobdata.eu/produkty/sklad/index/hladat/{{ $item->sku }}/hladaj_typ/kod" target="_blank" rel="noreferrer" class="block min-w-0 flex-1 overflow-hidden text-ellipsis whitespace-nowrap font-medium text-slate-900 hover:text-rose-500" title="{{ $item->product_name }}">{{ $item->product_name }}</a>
                                                @if ($item->current_supplier_portal_url)
                                                    <a href="{{ $item->current_supplier_portal_url }}" target="_blank" rel="noreferrer" class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-md bg-slate-100 text-[11px] font-bold text-slate-600 hover:bg-slate-200 hover:text-slate-900" title="Dodávateľ">
                                                        {{ $item->current_supplier_badge }}
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-3 text-center">
                                            <div class="leading-tight">
                                                <div class="max-w-[84px] overflow-hidden text-ellipsis whitespace-nowrap" title="{{ $item->sku }}">{{ $item->sku }}</div>
                                                @if ($item->supplier_code)
                                                    <div class="mt-0.5 max-w-[84px] select-text overflow-hidden text-ellipsis whitespace-nowrap text-[11px] font-medium text-slate-500" title="{{ $item->supplier_code }}">{{ $item->supplier_code }}</div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-3 py-3 text-center">
                                            <div class="leading-tight">
                                                <div>{{ $item->stock_code ?: '---' }}</div>
                                                @if ($item->current_supplier_stock_code)
                                                    <div class="mt-0.5 select-text text-[11px] font-medium text-slate-500">{{ $item->current_supplier_stock_code }}</div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-2 py-3 text-center">
                                            @if ($item->current_supplier_stock_status === 'in_stock')
                                                <span class="inline-flex rounded-md bg-emerald-100 px-2 py-1 text-[12px] font-bold text-emerald-700">Skladom</span>
                                            @elseif ($item->current_supplier_stock_status === 'out_of_stock')
                                                <span class="inline-flex rounded-md bg-rose-100 px-2 py-1 text-[12px] font-bold text-rose-700">Vypredané</span>
                                            @elseif ($item->current_supplier_stock_status === 'unknown')
                                                <span class="inline-flex rounded-md bg-slate-100 px-2 py-1 text-[12px] font-bold text-slate-500">Neznáme</span>
                                            @else
                                                <span class="text-slate-400">---</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-3 text-center">
                                            @php($statusClasses = match($item->status_meta['key']) { 'order' => 'bg-rose-100 text-rose-700', 'lost_sales' => 'bg-orange-100 text-orange-700', 'watch' => 'bg-amber-100 text-amber-700', 'price_check' => 'bg-violet-100 text-violet-700', 'stock' => 'bg-sky-100 text-sky-700', 'clearance' => 'bg-slate-200 text-slate-700', default => 'bg-slate-100 text-slate-700' })
                                            @if ($item->current_supplier_portal_url)
                                                <a href="{{ $item->current_supplier_portal_url }}" target="_blank" rel="noreferrer" class="inline-flex items-center rounded-md px-2 py-1 text-[12px] font-bold {{ $statusClasses }} hover:brightness-95" title="Otvoriť u dodávateľa">{{ $item->status_meta['label'] }}</a>
                                            @else
                                                <span class="inline-flex items-center rounded-md px-2 py-1 text-[12px] font-bold {{ $statusClasses }}">{{ $item->status_meta['label'] }}</span>
                                            @endif
                                        </td>
                                        <td class="px-0.5 py-3 text-center font-semibold {{ $item->stock_s1 === 0 ? 'text-red-600' : '' }}">{{ $item->stock_s1 }}</td>
                                        <td class="px-0.5 py-3 text-center">{{ $item->incoming_qty }}</td>
                                        <td class="px-0.5 py-3 text-center font-semibold {{ $item->stock_s2 === 0 ? 'text-red-600' : '' }}">{{ $item->stock_s2 }}</td>
                                        <td class="px-1 py-3 text-center font-semibold text-slate-900">
                                            <input
                                                type="number"
                                                min="0"
                                                name="quantities[{{ $item->sku }}]"
                                                value="{{ $item->recommended_s1_order }}"
                                                data-shared-sku="{{ $item->sku }}"
                                                data-recommended-qty="{{ $item->recommended_s1_order }}"
                                                oninput="this.closest('article').querySelectorAll('[data-shared-sku=&quot;{{ $item->sku }}&quot;]').forEach((input)=>{ if(input!==this) input.value=this.value })"
                                                class="w-[48px] rounded-md border border-slate-200 bg-rose-50 px-1.5 py-1 text-center text-sm font-semibold text-slate-900 focus:border-rose-300 focus:bg-white focus:outline-none"
                                            >
                                        </td>
                                        <td class="px-0.5 py-3 text-center font-semibold text-emerald-600">{{ $item->sales_period }}</td>
                                        <td class="px-0.5 py-3 text-center font-semibold text-teal-600">{{ $item->trend_t30 ?? '---' }}</td>
                                        <td class="px-0.5 py-3 text-center font-semibold text-blue-600">{{ $item->sales_p30 }}</td>
                                        <td class="px-0.5 py-3 text-center font-semibold text-red-600">{{ $item->sales_pop }}</td>
                                        <td class="px-1 py-3 text-center">{{ $item->s1_coverage_days !== null ? number_format(round($item->s1_coverage_days), 0, ',', ' ') : '---' }}</td>
                                        <td class="px-2 py-3 text-center">{{ number_format((float) $item->purchase_cost, 2, ',', ' ') }}</td>
                                        <td class="px-3 py-3 text-center">{{ $item->last_order_at?->format('d.m.Y') ?: '---' }}</td>
                                        <td class="px-2 py-3 text-center">{{ $item->product_age_days ?? '---' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if ($isSelectedSupplier)
                        </form>
                    @endif
                </article>
            @empty
                <section class="rounded-3xl border border-slate-200 bg-white px-5 py-16 text-center shadow-sm">
                    <strong class="block text-base font-semibold text-slate-900">Zatiaľ tu nie sú žiadne importované dáta.</strong>
                    <p class="mt-2 text-sm text-slate-500">Nahraj `predaj.xlsx` a modul vytvorí prvý Product Stats prehľad.</p>
                </section>
            @endforelse
        </section>

        <style>
            [data-product-stats-row] {
                cursor: pointer;
            }

            .product-stats-active-row {
                background: #cbd5e1 !important;
                box-shadow: inset 4px 0 0 #0ea5e9;
            }

            .product-stats-active-row > td {
                background: #cbd5e1 !important;
            }
        </style>

        <script>
            (() => {
                const storageKey = 'shoplink.productStats.activeRow';
                const rows = [...document.querySelectorAll('[data-product-stats-row]')];
                const continueButton = document.querySelector('[data-product-stats-continue]');

                const activate = (key, shouldScroll = false) => {
                    const row = rows.find((item) => item.dataset.productStatsRow === key);

                    rows.forEach((item) => item.classList.toggle('product-stats-active-row', item === row));
                    continueButton?.classList.toggle('hidden', !row);

                    if (row && shouldScroll) {
                        row.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                };

                rows.forEach((row) => {
                    row.addEventListener('pointerdown', () => {
                        localStorage.setItem(storageKey, row.dataset.productStatsRow);
                        activate(row.dataset.productStatsRow);
                    }, { capture: true });
                });

                continueButton?.addEventListener('click', () => activate(localStorage.getItem(storageKey), true));
                activate(localStorage.getItem(storageKey));
            })();
        </script>
    </div>
</x-app-layout>
