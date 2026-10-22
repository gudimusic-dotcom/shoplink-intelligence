<x-app-layout>
    <x-slot name="header">
        <div class="mx-auto max-w-[1720px] px-1">
            <section class="sl-card overflow-hidden">
                <div class="px-5 py-4 sm:px-6">
                    <div class="flex flex-col gap-3 xl:flex-row xl:items-center xl:justify-between">
                        <div>
                            <p class="sl-kicker">Product Stats</p>
                            <h1 class="mt-1 text-2xl font-semibold tracking-tight text-slate-50">Príprava zásob</h1>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="sl-pill">Riadky <strong class="text-slate-50">{{ $rows->count() }}</strong></span>
                            @if ($supplierFilter !== '')
                                <span class="sl-pill">Filter <strong class="text-slate-50">{{ $supplierFilter }}</strong></span>
                            @endif
                            @if ($lastSalesAt)
                                <span class="sl-pill">Sales dáta <strong class="text-slate-50">{{ $lastSalesAt }}</strong></span>
                            @endif
                            <a href="{{ route('product-stats.index') }}" class="sl-btn-secondary !px-3 !py-2">Product Stats</a>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </x-slot>

    <div class="sl-shell mt-3 space-y-3">
        <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-wrap items-end justify-between gap-3">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-[0.28em] text-slate-500">Report</p>
                    <h2 class="mt-1 text-xl font-semibold text-slate-900">Čo pripraviť na sklad</h2>
                    <p class="mt-1 text-sm text-slate-500">Výpočet: vlastný web a ostatné marketplace pre S1, Alza pre S2, Q4 minulý rok, sklad a tovar na ceste.</p>
                </div>
                <form method="GET" action="{{ route('product-stats.preparation') }}" class="flex flex-wrap items-end gap-2">
                    <div>
                        <label class="sl-form-label" style="color:#0f172a;">Dodávateľ</label>
                        <select name="supplier" class="sl-input !h-10 !w-[240px] !bg-white !text-slate-900">
                            <option value="">Všetci dodávatelia</option>
                            @foreach ($supplierOptions as $supplier)
                                <option value="{{ $supplier }}" @selected($supplierFilter === $supplier)>{{ $supplier }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="sl-btn-primary !py-2">Filtrovať</button>
                    <a href="{{ route('product-stats.preparation') }}" class="sl-btn-secondary !py-2">Reset</a>
                </form>
            </div>
            @if ($latestImport)
                <p class="mt-3 text-sm text-slate-500">XLS: {{ $latestImport->created_at?->format('d.m.Y H:i') }}</p>
            @endif
        </section>

        <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
            @if ($rows->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1260px] table-fixed text-sm">
                        <colgroup>
                            <col style="width: 390px;">
                            <col style="width: 105px;">
                            <col style="width: 120px;">
                            <col style="width: 52px;">
                            <col style="width: 52px;">
                            <col style="width: 54px;">
                            <col style="width: 62px;">
                            <col style="width: 62px;">
                            <col style="width: 62px;">
                            <col style="width: 56px;">
                            <col style="width: 76px;">
                            <col style="width: 72px;">
                            <col style="width: 94px;">
                            <col style="width: 160px;">
                        </colgroup>
                        <thead class="bg-slate-100 text-xs uppercase tracking-[0.14em] text-slate-700">
                            <tr>
                                <th class="px-4 py-2 text-left">Produkt</th><th class="px-3 py-2 text-left">SKU</th><th class="px-3 py-2 text-left">Dodávateľ</th><th class="px-2 py-2 text-center">S1</th><th class="px-2 py-2 text-center">S2</th><th class="px-2 py-2 text-center">Cesta</th><th class="px-2 py-2 text-center">S90<br>S1 / Alza</th><th class="px-2 py-2 text-center">S180<br>S1 / Alza</th><th class="px-2 py-2 text-center">Q4<br>S1 / Alza</th><th class="px-2 py-2 text-center">Dni</th><th class="px-2 py-2 text-center">Forecast<br>S1 / S2</th><th class="px-2 py-2 text-center">Obj.<br>S1 / S2</th><th class="px-2 py-2 text-center">Last</th><th class="px-3 py-2 text-left">Dôvod</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rows as $row)
                                <tr class="border-t border-slate-200 text-slate-900 hover:bg-slate-50">
                                    <td class="px-4 py-2"><a href="https://admin.mobdata.eu/produkty/sklad/index/hladat/{{ $row['sku'] }}/hladaj_typ/kod" target="_blank" rel="noreferrer" class="block overflow-hidden text-ellipsis whitespace-nowrap font-semibold text-slate-900 hover:text-rose-500" title="{{ $row['product_name'] }}">{{ $row['product_name'] }}</a></td>
                                    <td class="px-3 py-2">{{ $row['sku'] }}</td>
                                    <td class="px-3 py-2"><div class="flex items-center gap-2"><span>{{ $row['supplier'] }}</span>@if ($row['supplier_url'])<a href="{{ $row['supplier_url'] }}" target="_blank" rel="noreferrer" class="inline-flex h-7 w-7 items-center justify-center rounded-md bg-slate-100 text-[11px] font-bold text-slate-600 hover:bg-slate-200">{{ $row['supplier_badge'] }}</a>@endif</div></td>
                                    <td class="px-2 py-2 text-center {{ $row['stock_s1'] <= 0 ? 'font-semibold text-red-600' : '' }}">{{ $row['stock_s1'] }}</td><td class="px-2 py-2 text-center">{{ $row['stock_s2'] }}</td><td class="px-2 py-2 text-center">{{ $row['incoming_qty'] }}</td>
                                    <td class="px-2 py-2 text-center text-xs leading-5 font-semibold text-blue-600"><span class="block">{{ $row['sales_90_s1'] }}</span><span class="block text-amber-600">{{ $row['sales_90_s2'] }}</span></td>
                                    <td class="px-2 py-2 text-center text-xs leading-5"><span class="block">{{ $row['sales_180_s1'] }}</span><span class="block text-amber-600">{{ $row['sales_180_s2'] }}</span></td>
                                    <td class="px-2 py-2 text-center text-xs leading-5 font-semibold text-amber-600"><span class="block">{{ $row['sales_q4_s1'] }}</span><span class="block text-rose-600">{{ $row['sales_q4_s2'] }}</span></td>
                                    <td class="px-2 py-2 text-center">{{ $row['product_age_days'] ?: '---' }}</td>
                                    <td class="px-2 py-2 text-center text-xs leading-5 font-semibold"><span class="block">{{ $row['forecast_s1'] }}</span><span class="block text-amber-600">{{ $row['forecast_s2'] }}</span></td>
                                    <td class="px-2 py-2 text-center text-xs leading-5 font-bold text-rose-600"><span class="block">{{ $row['recommended_s1'] }}</span><span class="block text-amber-600">{{ $row['recommended_s2'] }}</span></td>
                                    <td class="px-2 py-2 text-center">{{ $row['last_order_at'] ?? '---' }}</td><td class="px-3 py-2 text-slate-600">{{ $row['reason'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="px-5 py-16 text-center"><strong class="block text-base font-semibold text-slate-900">Zatiaľ nie sú dáta pre prípravu.</strong><p class="mt-2 text-sm text-slate-500">Treba Product Stats import a Sales Intelligence riadky objednávok.</p></div>
            @endif
        </section>
    </div>
</x-app-layout>
