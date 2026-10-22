<x-app-layout>
    <x-slot name="header">
        <section class="sl-card overflow-hidden">
            <div class="px-5 py-4 sm:px-6">
                <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
                    <div>
                        <p class="sl-kicker">Sales Detail</p>
                        <h1 class="mt-2 text-2xl font-semibold tracking-tight text-slate-50">Produktové reporty</h1>
                        <p class="mt-1 text-sm text-slate-300">SKU, marža, objem, kategória, krajina a marketplace v jednom detailnom pohľade.</p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ route('sales.index', request()->only(['date_from', 'date_to'])) }}" class="sl-btn-secondary">Overview</a>
                        <a href="{{ route('sales.category-report', request()->only(['date_from', 'date_to'])) }}" class="sl-btn-secondary">Kategórie KPI</a>
                        <a href="{{ route('settings.index') }}" class="sl-btn-secondary">Nastavenia kurzov</a>
                    </div>
                </div>
            </div>
        </section>
    </x-slot>

    <div class="sl-shell mt-3 space-y-3">
        <section class="sl-card w-fit max-w-full p-3">
            <form method="GET" action="{{ route('sales.detail') }}" class="flex flex-wrap items-end gap-2">
                <label class="block w-[174px]">
                    <span class="mb-1.5 block text-xs font-semibold text-slate-300">Od</span>
                    <span class="sl-date-field">
                        <svg class="h-4 w-4 shrink-0 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M8 2v4"/><path d="M16 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/></svg>
                        <input type="text" name="date_from" value="{{ $dateFrom }}" placeholder="YYYY-MM-DD" pattern="\d{4}-\d{2}-\d{2}" class="sl-date-input" autocomplete="off">
                    </span>
                </label>
                <label class="block w-[174px]">
                    <span class="mb-1.5 block text-xs font-semibold text-slate-300">Do</span>
                    <span class="sl-date-field">
                        <svg class="h-4 w-4 shrink-0 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M8 2v4"/><path d="M16 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/></svg>
                        <input type="text" name="date_to" value="{{ $dateTo }}" placeholder="YYYY-MM-DD" pattern="\d{4}-\d{2}-\d{2}" class="sl-date-input" autocomplete="off">
                    </span>
                </label>
                <button type="submit" class="sl-btn-primary h-11 px-4 text-sm">Filtrovať</button>
                <a href="{{ route('sales.detail') }}" class="sl-btn-secondary h-11 px-4 text-sm">Reset</a>
                @if ($dateBounds?->first_date)
                    <span class="sl-pill h-11">{{ $dateBounds->first_date }} - {{ $dateBounds->last_date }}</span>
                @endif
            </form>
        </section>

        <section class="grid max-w-[1180px] gap-4 md:grid-cols-2 xl:grid-cols-5">
            <article class="sl-metric">
                <p class="sl-metric-label">Riadky</p>
                <div class="sl-metric-value">{{ number_format($lineCount, 0, ',', ' ') }}</div>
                <div class="sl-metric-delta">importované položky</div>
            </article>
            <article class="sl-metric">
                <p class="sl-metric-label">Predané kusy</p>
                <div class="sl-metric-value">{{ number_format($soldPieces, 0, ',', ' ') }}</div>
                <div class="sl-metric-delta">súčet quantity</div>
            </article>
            <article class="sl-metric">
                <p class="sl-metric-label">Obrat</p>
                <div class="sl-metric-value">{{ number_format($netRevenue, 0, ',', ' ') }} €</div>
                <div class="sl-metric-delta">bez DPH z riadkov</div>
            </article>
            <article class="sl-metric">
                <p class="sl-metric-label">Nákup</p>
                <div class="sl-metric-value">{{ number_format($purchaseCost, 0, ',', ' ') }} €</div>
                <div class="sl-metric-delta">importovaný purchase</div>
            </article>
            <article class="sl-metric">
                <p class="sl-metric-label">Marža</p>
                <div class="sl-metric-value">{{ number_format($marginRate, 1, ',', ' ') }}%</div>
                <div class="sl-metric-delta">{{ number_format($margin, 0, ',', ' ') }} €</div>
            </article>
        </section>

        <section class="sl-card max-w-[1500px] overflow-hidden">
            <div class="px-5 py-4">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="sl-kicker">Decision list</p>
                        <h2 class="sl-section-title mt-1">Produkty podľa obratu a signálu</h2>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <span class="sl-pill">Podporiť = dobrý objem a marža</span>
                        <span class="sl-pill">Cena = obrat existuje, marža slabá</span>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-[900px] w-full text-sm">
                    <thead class="text-left text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-400">
                        <tr class="bg-slate-900/35">
                            <th class="px-5 py-3">Produkt</th>
                            <th class="px-4 py-3">Signál</th>
                            <th class="px-4 py-3 text-right">Kusy</th>
                            <th class="px-4 py-3 text-right">Obrat</th>
                            <th class="px-4 py-3 text-right">Marža</th>
                            <th class="px-4 py-3 text-right">Marža %</th>
                            <th class="px-4 py-3 text-right">Marža / ks</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/40">
                        @forelse ($topRevenueProducts as $row)
                            <tr class="transition hover:bg-slate-700/35">
                                <td class="px-5 py-3">
                                    <div class="flex min-w-0 items-center gap-3">
                                        <div class="h-10 w-10 shrink-0 overflow-hidden rounded-lg bg-slate-700/70">
                                            @if ($row->image_url)
                                                <img src="{{ $row->image_url }}" alt="" class="h-full w-full object-cover">
                                            @else
                                                <div class="grid h-full w-full place-items-center text-xs font-semibold text-slate-400">{{ substr($row->model, 0, 2) }}</div>
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <strong class="block truncate text-slate-50">{{ $row->product_name ?? $row->model }}</strong>
                                            <span class="block truncate text-xs text-slate-400">{{ $row->model }} · {{ $row->brand ?? 'Bez výrobcu' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3"><span class="sl-status {{ $row->signal['class'] }}">{{ $row->signal['label'] }}</span></td>
                                <td class="px-4 py-3 text-right font-semibold text-slate-100">{{ number_format((float) $row->quantity_sum, 0, ',', ' ') }}</td>
                                <td class="px-4 py-3 text-right text-slate-200">{{ number_format((float) $row->net_revenue_eur, 0, ',', ' ') }} €</td>
                                <td class="px-4 py-3 text-right font-semibold {{ (float) $row->margin_eur < 0 ? 'text-rose-300' : 'text-emerald-300' }}">{{ number_format((float) $row->margin_eur, 0, ',', ' ') }} €</td>
                                <td class="px-4 py-3 text-right font-semibold text-slate-100">{{ number_format((float) $row->margin_rate, 1, ',', ' ') }}%</td>
                                <td class="px-4 py-3 text-right text-slate-200">{{ number_format((float) $row->margin_per_piece, 2, ',', ' ') }} €</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-6 text-sm text-slate-300">Nahraj riadky objednávok a produktový report sa vypočíta automaticky.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="grid max-w-[1500px] gap-5 xl:grid-cols-2">
            <article class="sl-card p-4">
                <p class="sl-kicker">Growth candidates</p>
                <h2 class="sl-section-title mt-1">Najlepšie produkty na podporu</h2>
                <div class="mt-4 space-y-2">
                    @forelse ($supportProducts as $row)
                        <div class="grid grid-cols-[minmax(0,1fr)_90px_90px_90px] items-center gap-3 rounded-lg bg-emerald-500/10 px-3 py-2.5 text-sm">
                            <div class="min-w-0">
                                <strong class="block truncate text-slate-50">{{ $row->product_name ?? $row->model }}</strong>
                                <span class="block truncate text-xs text-slate-400">{{ $row->model }} · {{ number_format((float) $row->quantity_sum, 0, ',', ' ') }} ks</span>
                            </div>
                            <div class="text-right font-semibold text-emerald-300">{{ number_format((float) $row->margin_eur, 0, ',', ' ') }} €</div>
                            <div class="text-right text-slate-200">{{ number_format((float) $row->margin_rate, 1, ',', ' ') }}%</div>
                            <div class="text-right text-slate-200">{{ number_format((float) $row->margin_per_piece, 2, ',', ' ') }} €/ks</div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-300">Zatiaľ nie je produkt s dostatočným objemom a zdravou maržou na podporu.</p>
                    @endforelse
                </div>
            </article>

            <article class="sl-card p-4">
                <p class="sl-kicker">Price control</p>
                <h2 class="sl-section-title mt-1">Vysoký obrat, slabá marža</h2>
                <div class="mt-4 space-y-2">
                    @forelse ($highVolumeLowMarginProducts as $row)
                        <div class="grid grid-cols-[minmax(0,1fr)_110px_90px] items-center gap-3 rounded-lg bg-amber-500/10 px-3 py-2.5 text-sm">
                            <div class="min-w-0">
                                <strong class="block truncate text-slate-50">{{ $row->product_name ?? $row->model }}</strong>
                                <span class="block truncate text-xs text-slate-400">{{ $row->model }} · {{ $row->category_name ?? 'Bez kategórie' }}</span>
                            </div>
                            <div class="text-right text-slate-200">{{ number_format((float) $row->net_revenue_eur, 0, ',', ' ') }} €</div>
                            <div class="text-right font-semibold text-amber-300">{{ number_format((float) $row->margin_rate, 1, ',', ' ') }}%</div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-300">V tomto období nie je výrazný produkt s vysokým obratom a slabou maržou.</p>
                    @endforelse
                </div>
            </article>
        </section>

        <section class="grid max-w-[1500px] gap-5 xl:grid-cols-3">
            <article class="sl-card p-4">
                <p class="sl-kicker">Categories</p>
                <h2 class="sl-section-title mt-1">Kategórie podľa marže</h2>
                <div class="mt-4 space-y-2">
                    @forelse ($categoryStats as $row)
                        <div class="rounded-lg bg-slate-700/35 px-3 py-2.5">
                            <div class="flex items-center justify-between gap-3 text-sm">
                                <strong class="truncate text-slate-50">{{ $row->category_name }}</strong>
                                <span class="font-semibold text-emerald-300">{{ number_format((float) $row->margin_eur, 0, ',', ' ') }} €</span>
                            </div>
                            <div class="mt-1 flex items-center justify-between text-xs text-slate-400">
                                <span>{{ number_format((float) $row->quantity_sum, 0, ',', ' ') }} ks</span>
                                <span>{{ number_format((float) $row->margin_rate, 1, ',', ' ') }}%</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-300">Kategórie nie sú dostupné.</p>
                    @endforelse
                </div>
            </article>

            <article class="sl-card p-4">
                <p class="sl-kicker">Marketplace</p>
                <h2 class="sl-section-title mt-1">Produktový výkon kanálov</h2>
                <div class="mt-4 space-y-2">
                    @forelse ($marketplaceProductStats as $row)
                        <div class="rounded-lg bg-slate-700/35 px-3 py-2.5">
                            <div class="flex items-center justify-between gap-3 text-sm">
                                <strong class="truncate text-slate-50">{{ $row->marketplace_display }}</strong>
                                <span class="font-semibold text-slate-100">{{ number_format((float) $row->net_revenue_eur, 0, ',', ' ') }} €</span>
                            </div>
                            <div class="mt-1 flex items-center justify-between text-xs text-slate-400">
                                <span>{{ number_format((float) $row->quantity_sum, 0, ',', ' ') }} ks</span>
                                <span>{{ number_format((float) $row->margin_rate, 1, ',', ' ') }}% marža</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-300">Marketplace dáta nie sú dostupné.</p>
                    @endforelse
                </div>
            </article>

            <article class="sl-card p-4">
                <p class="sl-kicker">Countries</p>
                <h2 class="sl-section-title mt-1">Produktový výkon krajín</h2>
                <div class="mt-4 space-y-2">
                    @forelse ($countryProductStats as $row)
                        <div class="rounded-lg bg-slate-700/35 px-3 py-2.5">
                            <div class="flex items-center justify-between gap-3 text-sm">
                                <strong class="truncate text-slate-50">{{ $row->portal_country }}</strong>
                                <span class="font-semibold text-slate-100">{{ number_format((float) $row->net_revenue_eur, 0, ',', ' ') }} €</span>
                            </div>
                            <div class="mt-1 flex items-center justify-between text-xs text-slate-400">
                                <span>{{ number_format((float) $row->quantity_sum, 0, ',', ' ') }} ks</span>
                                <span>{{ number_format((float) $row->margin_rate, 1, ',', ' ') }}% marža</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-300">Krajiny nie sú dostupné.</p>
                    @endforelse
                </div>
            </article>
        </section>

        @if ($negativeMarginProducts->isNotEmpty())
            <section class="sl-card max-w-[1180px] p-4">
                <p class="sl-kicker">Critical</p>
                <h2 class="sl-section-title mt-1">Produkty so zápornou maržou</h2>
                <div class="mt-4 space-y-2">
                    @foreach ($negativeMarginProducts as $row)
                        <div class="grid gap-2 rounded-lg bg-rose-500/10 px-3 py-2.5 text-sm md:grid-cols-[minmax(0,1fr)_110px_90px] md:items-center">
                            <div class="min-w-0">
                                <strong class="block truncate text-slate-50">{{ $row->product_name ?? $row->model }}</strong>
                                <span class="block truncate text-xs text-slate-400">{{ $row->model }} · {{ $row->brand ?? 'Bez výrobcu' }}</span>
                            </div>
                            <div class="text-right text-slate-200">{{ number_format((float) $row->net_revenue_eur, 0, ',', ' ') }} €</div>
                            <div class="text-right font-semibold text-rose-300">{{ number_format((float) $row->margin_eur, 0, ',', ' ') }} €</div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</x-app-layout>
