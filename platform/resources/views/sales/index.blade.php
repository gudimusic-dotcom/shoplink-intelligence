<x-app-layout>
    <x-slot name="header">
        <section class="sl-card overflow-hidden">
            <div class="px-5 py-4 sm:px-6">
                <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
                    <div>
                        <p class="sl-kicker">Sales Intelligence</p>
                        <h1 class="mt-2 text-2xl font-semibold tracking-tight text-slate-50">Orders Overview</h1>
                        <p class="mt-1 text-sm text-slate-300">Manažérsky prehľad objednávok: kanály, krajiny, marketplace a vývoj v čase.</p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ route('sales.top30') }}" class="sl-btn-primary">Top 30</a>
                        <a href="{{ route('sales.category-report', request()->only(['date_from', 'date_to', 'category_level'])) }}" class="sl-btn-secondary">Kategórie KPI</a>
                        <a href="{{ route('sales.detail', request()->only(['date_from', 'date_to'])) }}" class="sl-btn-secondary">Sales Detail</a>
                        <a href="{{ route('settings.index') }}" class="sl-btn-secondary">Nastavenia kurzov</a>
                        <a href="#orders-import" class="sl-btn-primary">Import XLSX</a>
                    </div>
                </div>
            </div>
        </section>
    </x-slot>

    <div class="sl-shell mt-3 space-y-3">
        @if (session('status'))
            <div class="flash-banner">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="rounded-xl bg-rose-500/10 px-4 py-3 text-sm text-rose-200">
                <strong class="block font-semibold">Operácia neprebehla.</strong>
                <span>Skontroluj vstupné údaje.</span>
            </div>
        @endif

        <section class="sl-card w-fit max-w-full p-3">
            <form method="GET" action="{{ route('sales.index') }}" class="flex flex-wrap items-end gap-2">
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
                <a href="{{ route('sales.index') }}" class="sl-btn-secondary h-11 px-4 text-sm">Reset</a>
            </form>
        </section>

        <section class="grid max-w-[1180px] gap-4 md:grid-cols-2 xl:grid-cols-4">
            <article class="sl-metric">
                <p class="sl-metric-label">Objednávky</p>
                <div class="sl-metric-value">{{ number_format($completedOrders, 0, ',', ' ') }}</div>
                <div class="sl-metric-delta">z riadkov objednávok</div>
            </article>
            <article class="sl-metric">
                <p class="sl-metric-label">Marketplace share</p>
                <div class="sl-metric-value">{{ number_format($marketplaceShare, 1, ',', ' ') }}%</div>
                <div class="sl-metric-delta">{{ number_format($marketplaceOrders, 0, ',', ' ') }} objednávok</div>
            </article>
            <article class="sl-metric">
                <p class="sl-metric-label">Vlastný web</p>
                <div class="sl-metric-value">{{ number_format($ownWebShare, 1, ',', ' ') }}%</div>
                <div class="sl-metric-delta">{{ number_format($ownWebOrders, 0, ',', ' ') }} objednávok</div>
            </article>
            <article class="sl-metric">
                <p class="sl-metric-label">EUR obrat</p>
                <div class="sl-metric-value">{{ number_format($eurRevenue, 0, ',', ' ') }} €</div>
                <div class="sl-metric-delta">{{ $missingRates->count() > 0 ? 'chýbajú kurzy: '.$missingRates->implode(', ') : 'všetky meny prepočítané' }}</div>
            </article>
        </section>


        <section class="grid max-w-[1180px] gap-4 md:grid-cols-2 xl:grid-cols-4">
            <article class="sl-metric">
                <p class="sl-metric-label">Product rows</p>
                <div class="sl-metric-value">{{ number_format($lineCount, 0, ',', ' ') }}</div>
                <div class="sl-metric-delta">importované riadky</div>
            </article>
            <article class="sl-metric">
                <p class="sl-metric-label">Produktový obrat</p>
                <div class="sl-metric-value">{{ number_format($productNetRevenue, 0, ',', ' ') }} €</div>
                <div class="sl-metric-delta">Cena bez DPH z riadkov</div>
            </article>
            <article class="sl-metric">
                <p class="sl-metric-label">Produktová marža</p>
                <div class="sl-metric-value">{{ number_format($productMargin, 0, ',', ' ') }} €</div>
                <div class="sl-metric-delta">{{ number_format($productMarginRate, 1, ',', ' ') }}% z obratu</div>
            </article>
            <article class="sl-metric">
                <p class="sl-metric-label">Predané kusy</p>
                <div class="sl-metric-value">{{ number_format($soldPieces, 0, ',', ' ') }}</div>
                <div class="sl-metric-delta">súčet Počet (ks)</div>
            </article>
        </section>

        <section class="sl-card max-w-[1180px] p-4">
            <p class="sl-kicker">Product margin</p>
            <h2 class="sl-section-title mt-1">Top produkty podľa marže</h2>
            <div class="mt-5 grid gap-3 xl:grid-cols-2">
                @forelse ($topProductMargins as $row)
                    @php
                        $width = max(5, round(((float) $row->margin_eur / $maxProductMargin) * 100));
                        $marginRate = (float) $row->net_revenue_eur > 0 ? round(((float) $row->margin_eur / (float) $row->net_revenue_eur) * 100, 1) : 0;
                    @endphp
                    <article class="rounded-lg bg-slate-700/45 px-4 py-3">
                        <div class="flex items-start gap-3">
                            <div class="h-14 w-14 shrink-0 overflow-hidden rounded-lg bg-slate-800">
                                @if ($row->image_url)
                                    <img src="{{ $row->image_url }}" alt="" class="h-full w-full object-cover">
                                @else
                                    <div class="grid h-full w-full place-items-center text-xs font-semibold text-slate-500">{{ substr($row->model, 0, 2) }}</div>
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <strong class="block truncate text-sm font-semibold text-slate-50">{{ $row->product_name ?? $row->model }}</strong>
                                <span class="mt-0.5 block truncate text-xs text-slate-400">{{ $row->model }} · {{ $row->brand ?? 'Bez výrobcu' }} · {{ number_format((float) $row->quantity_sum, 0, ',', ' ') }} ks</span>
                                <div class="mt-3 h-2 rounded-full bg-slate-800"><div class="h-2 rounded-full bg-emerald-400" style="width: {{ $width }}%"></div></div>
                            </div>
                            <div class="shrink-0 text-right">
                                <strong class="block text-sm font-semibold text-slate-50">{{ number_format((float) $row->margin_eur, 0, ',', ' ') }} €</strong>
                                <span class="text-xs text-slate-400">{{ number_format($marginRate, 1, ',', ' ') }}%</span>
                            </div>
                        </div>
                    </article>
                @empty
                    <p class="text-sm text-slate-300">Nahraj export riadkov objednávok a tu sa zobrazí produktový margin.</p>
                @endforelse
            </div>
        </section>

        <section class="sl-card max-w-[1180px] p-4">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="sl-kicker">Categories</p>
                    <h2 class="sl-section-title mt-1">Najsilnejšie kategórie podľa obratu</h2>
                </div>
                <div class="flex flex-wrap gap-2">
                    @foreach (['2' => '2. úroveň', '3' => '3. úroveň', 'leaf' => 'posledná'] as $level => $label)
                        <a
                            href="{{ route('sales.index', array_merge(request()->only(['date_from', 'date_to']), ['category_level' => $level])) }}"
                            class="{{ $categoryLevel === $level ? 'sl-btn-primary' : 'sl-btn-secondary' }} h-9 px-3 text-xs"
                        >
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </div>
            <div class="mt-5 space-y-4">
                @forelse ($categoryStats as $row)
                    @php
                        $width = max(5, round(((float) $row->net_revenue_eur / $maxCategoryRevenue) * 100));
                    @endphp
                    <div class="rounded-lg bg-slate-700/45 px-4 py-3">
                        <div class="flex items-center justify-between gap-3 text-sm">
                            <div class="min-w-0">
                                <strong class="block truncate text-slate-50">{{ $row->category_leaf ?? $row->category_name }}</strong>
                                @if ($row->category_parent)
                                    <span class="mt-0.5 block truncate text-xs text-slate-400">{{ $row->category_parent }}</span>
                                @endif
                            </div>
                            <span class="text-slate-300">{{ number_format((float) $row->net_revenue_eur, 0, ',', ' ') }} €</span>
                        </div>
                        <div class="mt-2 h-2 rounded-full bg-slate-800"><div class="h-2 rounded-full bg-rose-500" style="width: {{ $width }}%"></div></div>
                        <div class="mt-2 flex flex-wrap gap-x-4 gap-y-1 text-xs font-medium text-slate-400">
                            <span>{{ number_format((float) $row->quantity_sum, 0, ',', ' ') }} ks</span>
                            <span>Marža {{ number_format((float) $row->margin_eur, 0, ',', ' ') }} €</span>
                            <span>{{ number_format((float) $row->margin_rate, 1, ',', ' ') }}%</span>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-300">Pre zvolené obdobie nie sú dáta podľa kategórií.</p>
                @endforelse
            </div>
        </section>

        <section class="grid max-w-[1180px] gap-5 xl:grid-cols-2">
            <article class="sl-card p-4">
                <p class="sl-kicker">Channel mix</p>
                <h2 class="sl-section-title mt-1">Marketplace vs vlastný web</h2>

                <div class="mt-5 grid gap-5 md:grid-cols-[170px_minmax(0,1fr)] md:items-center">
                    <div class="grid place-items-center">
                        <div class="relative h-40 w-40 rounded-full" style="background: conic-gradient(#f43f5e 0 {{ $marketplaceShare }}%, #64748b {{ $marketplaceShare }}% 100%);">
                            <div class="absolute inset-7 grid place-items-center rounded-full bg-slate-800 text-center">
                                <strong class="text-2xl font-semibold text-slate-50">{{ number_format($marketplaceShare, 0, ',', ' ') }}%</strong>
                                <span class="text-xs font-semibold uppercase tracking-[0.12em] text-slate-400">marketplace</span>
                            </div>
                        </div>
                    </div>

                    <div class="grid gap-3">
                        <div class="rounded-lg bg-slate-700/55 px-4 py-3 text-sm">
                            <div class="flex items-center justify-between gap-3">
                                <span class="flex items-center gap-2 text-slate-100"><span class="h-2.5 w-2.5 rounded-full bg-rose-500"></span>Marketplace</span>
                                <strong class="text-slate-50">{{ number_format($marketplaceOrders, 0, ',', ' ') }}</strong>
                            </div>
                            <div class="mt-2 h-2 rounded-full bg-slate-800"><div class="h-2 rounded-full bg-rose-500" style="width: {{ $marketplaceShare }}%"></div></div>
                        </div>
                        <div class="rounded-lg bg-slate-700/55 px-4 py-3 text-sm">
                            <div class="flex items-center justify-between gap-3">
                                <span class="flex items-center gap-2 text-slate-100"><span class="h-2.5 w-2.5 rounded-full bg-slate-400"></span>Vlastný web</span>
                                <strong class="text-slate-50">{{ number_format($ownWebOrders, 0, ',', ' ') }}</strong>
                            </div>
                            <div class="mt-2 h-2 rounded-full bg-slate-800"><div class="h-2 rounded-full bg-slate-400" style="width: {{ $ownWebShare }}%"></div></div>
                        </div>
                    </div>
                </div>
            </article>

            <article class="sl-card p-4">
                <p class="sl-kicker">Monthly trend</p>
                <h2 class="sl-section-title mt-1">Mesačný vývoj objednávok</h2>
                <div class="mt-5 overflow-x-auto pb-1">
                    <div class="flex h-52 min-w-max items-end gap-2 border-b border-slate-700/80 pb-4">
                        @forelse ($monthlyStats as $row)
                            @php
                                $height = max(8, round(($row->orders_count / $maxMonthlyOrders) * 100));
                            @endphp
                            <div class="flex w-16 shrink-0 flex-col items-center gap-2">
                                <div class="flex h-32 w-full items-end rounded-md bg-slate-700/30 px-1.5">
                                    <div class="w-full rounded-sm bg-rose-500/90" style="height: {{ $height }}%"></div>
                                </div>
                                <span class="text-xs font-medium text-slate-300">{{ $row->month }}</span>
                                <strong class="text-xs font-semibold text-slate-50">{{ number_format($row->orders_count, 0, ',', ' ') }}</strong>
                            </div>
                        @empty
                            <p class="self-center text-sm text-slate-300">Pre zvolené obdobie nie sú dáta.</p>
                        @endforelse
                    </div>
                </div>
            </article>

            <article class="sl-card p-4 xl:col-span-2">
                <div class="flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="sl-kicker">Monthly channel mix</p>
                        <h2 class="sl-section-title mt-1">Marketplace vs e-shop po mesiacoch</h2>
                    </div>
                    <span class="text-xs font-medium text-slate-400">podiel objednávok · obrat</span>
                </div>

                <div class="mt-5 overflow-x-auto pb-1">
                    <div class="flex min-w-max gap-3">
                        @forelse ($monthlyChannelMix as $row)
                            @php
                                $ordersCount = max(1, (int) $row->orders_count);
                                $marketplaceShareMonthly = round(((int) $row->marketplace_orders / $ordersCount) * 100, 1);
                                $ownWebShareMonthly = max(0, 100 - $marketplaceShareMonthly);
                            @endphp
                            <article class="w-36 shrink-0 rounded-lg bg-slate-700/45 p-3">
                                <div class="flex items-center justify-between gap-2">
                                    <strong class="text-sm text-slate-50">{{ $row->month }}</strong>
                                    <span class="text-xs text-slate-400">{{ number_format((int) $row->orders_count, 0, ',', ' ') }} obj.</span>
                                </div>

                                <div class="mt-3 flex h-2.5 overflow-hidden rounded-full bg-slate-800">
                                    <div class="h-full bg-rose-500" style="width: {{ $marketplaceShareMonthly }}%"></div>
                                    <div class="h-full bg-slate-400" style="width: {{ $ownWebShareMonthly }}%"></div>
                                </div>

                                <div class="mt-3 grid gap-1 text-xs font-medium">
                                    <div class="flex items-center justify-between gap-2 text-slate-300">
                                        <span>MP</span>
                                        <strong class="text-slate-50">{{ number_format($marketplaceShareMonthly, 1, ',', ' ') }}%</strong>
                                    </div>
                                    <div class="flex items-center justify-between gap-2 text-slate-300">
                                        <span>E-shop</span>
                                        <strong class="text-slate-50">{{ number_format($ownWebShareMonthly, 1, ',', ' ') }}%</strong>
                                    </div>
                                    <div class="pt-1 text-slate-400">{{ number_format((float) $row->revenue_eur, 0, ',', ' ') }} €</div>
                                </div>
                            </article>
                        @empty
                            <p class="text-sm text-slate-300">Pre zvolené obdobie nie sú mesačné dáta podľa kanála.</p>
                        @endforelse
                    </div>
                </div>
            </article>
        </section>

        <section class="grid max-w-[1180px] gap-5 xl:grid-cols-2">
            <article class="sl-card p-4">
                @php
                    $colors = ['#f43f5e', '#f59e0b', '#22c55e', '#38bdf8', '#a78bfa', '#f97316', '#14b8a6', '#e879f9'];
                    $marketplaceTotal = max(1, (int) $marketplaceStats->sum('orders_count'));
                    $marketplaceStart = 0;
                    $marketplaceSegments = [];
                    foreach ($marketplaceStats as $index => $row) {
                        $marketplaceEnd = $marketplaceStart + (((int) $row->orders_count / $marketplaceTotal) * 100);
                        $marketplaceSegments[] = $colors[$index % count($colors)].' '.$marketplaceStart.'% '.$marketplaceEnd.'%';
                        $marketplaceStart = $marketplaceEnd;
                    }
                    $marketplaceGradient = count($marketplaceSegments) > 0 ? implode(', ', $marketplaceSegments) : '#334155 0% 100%';
                @endphp
                <p class="sl-kicker">Marketplaces</p>
                <h2 class="sl-section-title mt-1">Podiel marketplace</h2>
                <div class="mt-5 grid gap-5 sm:grid-cols-[150px_minmax(0,1fr)] sm:items-center">
                    <div class="grid place-items-center">
                        <div class="relative h-36 w-36 rounded-full" style="background: conic-gradient({{ $marketplaceGradient }});">
                            <div class="absolute inset-6 grid place-items-center rounded-full bg-slate-800 text-center">
                                <strong class="text-xl font-semibold text-slate-50">{{ number_format($marketplaceTotal, 0, ',', ' ') }}</strong>
                                <span class="text-xs text-slate-400">obj.</span>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-3">
                        @forelse ($marketplaceStats as $index => $row)
                            @php
                                $share = $marketplaceTotal > 0 ? round(((int) $row->orders_count / $marketplaceTotal) * 100, 1) : 0;
                            @endphp
                            <div>
                                <div class="mb-1.5 flex items-center justify-between gap-3 text-sm">
                                    <strong class="truncate text-slate-50"><span class="mr-2 inline-block h-2.5 w-2.5 rounded-full" style="background: {{ $colors[$index % count($colors)] }}"></span>{{ $row->marketplace_display }}</strong>
                                    <span class="text-slate-300">{{ number_format($share, 1, ',', ' ') }}% ({{ number_format($row->orders_count, 0, ',', ' ') }})</span>
                                </div>
                                <div class="h-2 rounded-full bg-slate-700/70"><div class="h-2 rounded-full" style="width: {{ max(5, round(($row->orders_count / $maxMarketplaceOrders) * 100)) }}%; background: {{ $colors[$index % count($colors)] }}"></div></div>
                            </div>
                        @empty
                            <p class="text-sm text-slate-300">Zatiaľ nie sú importované marketplace objednávky.</p>
                        @endforelse
                    </div>
                </div>
            </article>

            <article class="sl-card p-4">
                @php
                    $countryTotal = max(1, (int) $countryStats->sum('orders_count'));
                    $countryStart = 0;
                    $countrySegments = [];
                    foreach ($countryStats as $index => $row) {
                        $countryEnd = $countryStart + (((int) $row->orders_count / $countryTotal) * 100);
                        $countrySegments[] = $colors[$index % count($colors)].' '.$countryStart.'% '.$countryEnd.'%';
                        $countryStart = $countryEnd;
                    }
                    $countryGradient = count($countrySegments) > 0 ? implode(', ', $countrySegments) : '#334155 0% 100%';
                @endphp
                <p class="sl-kicker">Countries</p>
                <h2 class="sl-section-title mt-1">Podiel krajín</h2>
                <div class="mt-5 grid gap-5 sm:grid-cols-[150px_minmax(0,1fr)] sm:items-center">
                    <div class="grid place-items-center">
                        <div class="relative h-36 w-36 rounded-full" style="background: conic-gradient({{ $countryGradient }});">
                            <div class="absolute inset-6 grid place-items-center rounded-full bg-slate-800 text-center">
                                <strong class="text-xl font-semibold text-slate-50">{{ number_format($countryTotal, 0, ',', ' ') }}</strong>
                                <span class="text-xs text-slate-400">obj.</span>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-3">
                        @forelse ($countryStats as $index => $row)
                            @php
                                $share = $countryTotal > 0 ? round(((int) $row->orders_count / $countryTotal) * 100, 1) : 0;
                            @endphp
                            <div>
                                <div class="mb-1.5 flex items-center justify-between gap-3 text-sm">
                                    <strong class="truncate text-slate-50"><span class="mr-2 inline-block h-2.5 w-2.5 rounded-full" style="background: {{ $colors[$index % count($colors)] }}"></span>{{ $row->portal_country ?? 'Bez krajiny' }}</strong>
                                    <span class="text-slate-300">{{ number_format($share, 1, ',', ' ') }}% ({{ number_format($row->orders_count, 0, ',', ' ') }})</span>
                                </div>
                                <div class="h-2 rounded-full bg-slate-700/70"><div class="h-2 rounded-full" style="width: {{ max(5, round(($row->orders_count / $maxCountryOrders) * 100)) }}%; background: {{ $colors[$index % count($colors)] }}"></div></div>
                            </div>
                        @empty
                            <p class="text-sm text-slate-300">Pre zvolené obdobie nie sú dáta podľa krajín.</p>
                        @endforelse
                    </div>
                </div>
            </article>


            <article class="sl-card p-4 xl:col-span-2">
                <div class="flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="sl-kicker">Country marketplace mix</p>
                        <h2 class="sl-section-title mt-1">Marketplace podiel podľa krajiny</h2>
                    </div>
                    <span class="text-xs font-medium text-slate-400">objednávky · EUR podiel</span>
                </div>

                <div class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                    @forelse ($countryMarketplaceMix as $row)
                        @php
                            $marketplaceShareByOrders = (int) $row->orders_count > 0 ? round(((int) $row->marketplace_orders / (int) $row->orders_count) * 100, 1) : 0;
                            $ownWebShareByOrders = max(0, 100 - $marketplaceShareByOrders);
                            $marketplaceShareByRevenue = (float) $row->revenue_eur > 0 ? round(((float) $row->marketplace_revenue_eur / (float) $row->revenue_eur) * 100, 1) : 0;
                        @endphp
                        <article class="rounded-lg bg-slate-700/45 p-3">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <strong class="block text-lg font-semibold text-slate-50">{{ $row->portal_country }}</strong>
                                    <span class="text-xs text-slate-400">{{ number_format($row->orders_count, 0, ',', ' ') }} obj.</span>
                                </div>
                                <div class="text-right">
                                    <strong class="block text-base font-semibold text-slate-50">{{ number_format($marketplaceShareByOrders, 1, ',', ' ') }}%</strong>
                                    <span class="text-xs text-slate-400">EUR {{ number_format($marketplaceShareByRevenue, 1, ',', ' ') }}%</span>
                                </div>
                            </div>

                            <div class="mt-3 flex h-2.5 overflow-hidden rounded-full bg-slate-800">
                                <div class="h-full bg-rose-500" style="width: {{ $marketplaceShareByOrders }}%"></div>
                                <div class="h-full bg-slate-500" style="width: {{ $ownWebShareByOrders }}%"></div>
                            </div>

                            <div class="mt-3 flex items-center justify-between gap-3 text-[11px] font-medium text-slate-300">
                                <span>MP {{ number_format($row->marketplace_orders, 0, ',', ' ') }}</span>
                                <span class="text-right">Web {{ number_format($row->own_web_orders, 0, ',', ' ') }}</span>
                            </div>
                        </article>
                    @empty
                        <p class="text-sm text-slate-300">Pre zvolené obdobie nie sú dáta podľa krajín.</p>
                    @endforelse
                </div>
            </article>

            <article class="sl-card p-4 xl:col-span-2">
                <p class="sl-kicker">Revenue</p>
                <h2 class="sl-section-title mt-1">EUR obrat podľa kanála</h2>
                <div class="mt-5 space-y-4">
                    @forelse ($channelRevenueStats as $row)
                        @php
                            $width = max(5, round(((float) $row->revenue_eur / $maxChannelRevenue) * 100));
                        @endphp
                        <div class="rounded-lg bg-slate-700/45 px-4 py-3">
                            <div class="flex items-center justify-between gap-3 text-sm">
                                <strong class="capitalize text-slate-50">{{ str_replace('_', ' ', $row->channel_type ?? 'unknown') }}</strong>
                                <span class="text-slate-300">{{ number_format((float) $row->revenue_eur, 0, ',', ' ') }} €</span>
                            </div>
                            <div class="mt-2 h-2 rounded-full bg-slate-800"><div class="h-2 rounded-full bg-emerald-400" style="width: {{ $width }}%"></div></div>
                            <div class="mt-2 text-xs font-medium text-slate-400">{{ number_format($row->orders_count, 0, ',', ' ') }} objednávok</div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-300">Pre zvolené obdobie nie je prepočítaný EUR obrat.</p>
                    @endforelse
                </div>
            </article>
        </section>

        <section class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_420px]">
            <article class="sl-card p-5" id="orders-import">
                <p class="sl-kicker">Import</p>
                <h2 class="sl-section-title mt-1">Mobdata riadky objednávok</h2>
                <p class="sl-section-copy mt-1">Nahraj XLSX export riadkov. Ak import pokrýva už existujúce dátumy, staré riadky za toto obdobie sa nahradia novými.</p>

                <div class="mt-5 grid gap-4 xl:grid-cols-2">
                    <form method="POST" action="{{ route('sales.import-lines') }}" enctype="multipart/form-data" class="rounded-lg bg-slate-700/35 p-3">
                        @csrf
                        <label class="mb-1.5 block text-xs font-semibold text-slate-100">Riadky objednávok XLSX</label>
                        <input name="order_lines_file" type="file" accept=".xlsx" class="block w-full rounded-lg bg-slate-700/65 px-3 py-2 text-sm text-slate-100 file:mr-4 file:rounded-md file:border-0 file:bg-rose-600 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-white">
                        <button type="submit" class="sl-btn-primary mt-3 w-full">Importovať riadky</button>
                    </form>

                    <form method="POST" action="{{ route('sales.import-products') }}" enctype="multipart/form-data" class="rounded-lg bg-slate-700/35 p-3">
                        @csrf
                        <label class="mb-1.5 block text-xs font-semibold text-slate-100">Google Merchant produkty</label>
                        <input name="products_file" type="file" accept=".xml" class="block w-full rounded-lg bg-slate-700/65 px-3 py-2 text-sm text-slate-100 file:mr-4 file:rounded-md file:border-0 file:bg-rose-600 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-white">
                        <button type="submit" class="sl-btn-primary mt-3 w-full">Importovať produkty</button>
                    </form>
                </div>
            </article>

            <aside class="sl-card p-5">
                <p class="sl-kicker">Import log</p>
                <h2 class="sl-section-title mt-1">Posledné importy</h2>
                <div class="mt-5 space-y-3">
                    @forelse ($latestImports as $job)
                        <div class="rounded-lg bg-slate-700/45 px-4 py-3 text-sm">
                            <strong class="block truncate text-slate-50">{{ $job->file_name }}</strong>
                            <div class="mt-2 grid grid-cols-4 gap-2 text-xs text-slate-300">
                                <span>{{ $job->import_type === 'product_catalog' ? 'prod.' : 'riadky' }}</span>
                                <span>{{ $job->imported_rows }} nových</span>
                                <span>{{ $job->skipped_rows }} skip</span>
                                <span>{{ $job->warning_count }} warn</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-300">Zatiaľ neprebehol žiadny import objednávok.</p>
                    @endforelse
                </div>
            </aside>
        </section>
    </div>
</x-app-layout>
