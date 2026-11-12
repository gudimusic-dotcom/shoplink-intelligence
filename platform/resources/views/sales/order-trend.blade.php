<x-app-layout>
    <x-slot name="header">
        <section class="sl-card overflow-hidden">
            <div class="px-5 py-4 sm:px-6">
                <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
                    <div>
                        <p class="sl-kicker">Sales Intelligence</p>
                        <h1 class="mt-2 text-2xl font-semibold tracking-tight text-slate-50">Trend objednávok</h1>
                        <p class="mt-1 text-sm text-slate-300">Týždenný vývoj objednávok s porovnaním rovnakého obdobia minulého roka.</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ route('sales.index') }}" class="sl-btn-secondary">Orders Overview</a>
                        <a href="{{ route('sales.top30') }}" class="sl-btn-primary">Top 30</a>
                    </div>
                </div>
            </div>
        </section>
    </x-slot>

    <div class="sl-shell mt-3 space-y-3">
        <section class="sl-card p-4">
            <form method="GET" action="{{ route('sales.order-trend') }}" class="flex flex-wrap items-end gap-3">
                <label class="block w-[174px]">
                    <span class="mb-1.5 block text-xs font-semibold text-slate-300">Od</span>
                    <input type="date" name="date_from" value="{{ $dateFrom }}" class="sl-input !h-11 !bg-white !text-slate-900">
                </label>
                <label class="block w-[174px]">
                    <span class="mb-1.5 block text-xs font-semibold text-slate-300">Do</span>
                    <input type="date" name="date_to" value="{{ $dateTo }}" class="sl-input !h-11 !bg-white !text-slate-900">
                </label>
                <label class="block w-[240px]">
                    <span class="mb-1.5 block text-xs font-semibold text-slate-300">Predajný kanál</span>
                    <select name="channel" class="sl-input !h-11 !bg-white !text-slate-900">
                        <option value="all" @selected($channel === 'all')>Všetky kanály</option>
                        <option value="own_web" @selected($channel === 'own_web')>Mobilego web</option>
                        <option value="marketplace" @selected($channel === 'marketplace')>Všetky marketplace</option>
                        @foreach ($marketplaces as $marketplace)
                            <option value="{{ $marketplace }}" @selected($channel === $marketplace)>{{ $marketplace }}</option>
                        @endforeach
                    </select>
                </label>
                <button type="submit" class="sl-btn-primary h-11 px-4 text-sm">Zobraziť trend</button>
                <a href="{{ route('sales.order-trend') }}" class="sl-btn-secondary h-11 px-4 text-sm">Reset</a>
            </form>
        </section>

        <section class="grid max-w-[1180px] gap-4 md:grid-cols-3">
            <article class="sl-metric">
                <p class="sl-metric-label">Vybrané obdobie</p>
                <div class="sl-metric-value">{{ number_format($currentTotal, 0, ',', ' ') }}</div>
                <div class="sl-metric-delta">Objednávky {{ $dateFrom }} – {{ $dateTo }}</div>
            </article>
            <article class="sl-metric">
                <p class="sl-metric-label">Minulý rok</p>
                <div class="sl-metric-value">{{ number_format($previousTotal, 0, ',', ' ') }}</div>
                <div class="sl-metric-delta">Rovnaké obdobie {{ $previousFrom }} – {{ $previousTo }}</div>
            </article>
            <article class="sl-metric">
                <p class="sl-metric-label">Rozdiel</p>
                <div class="sl-metric-value {{ $difference !== null && $difference < 0 ? 'text-rose-400' : 'text-emerald-400' }}">{{ $difference !== null ? number_format($difference, 1, ',', ' ').' %' : '---' }}</div>
                <div class="sl-metric-delta">oproti minulému roku</div>
            </article>
        </section>

        <section class="sl-card p-5">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="sl-kicker">Týždenný vývoj</p>
                    <h2 class="sl-section-title mt-1">Kedy začínajú rásť objednávky?</h2>
                </div>
                <div class="flex items-center gap-4 text-xs text-slate-600">
                    <span><i class="mr-1 inline-block h-3 w-3 rounded-sm bg-rose-500"></i>Vybrané obdobie</span>
                    <span><i class="mr-1 inline-block h-3 w-3 rounded-sm bg-slate-400"></i>Minulý rok</span>
                </div>
            </div>

            <div class="mt-6 overflow-x-auto pb-2">
                <div class="flex min-w-[760px] items-end gap-2" style="height: 330px;">
                    @foreach ($chart as $point)
                        <div class="flex min-w-[54px] flex-1 flex-col items-center justify-end gap-2">
                            <div class="flex h-[270px] w-full items-end justify-center gap-1">
                                <div class="group relative w-1/2 max-w-[28px] rounded-t-md bg-rose-500" style="height: {{ $point['current'] > 0 ? max(3, ($point['current'] / $maxOrders) * 100) : 0 }}%;" title="{{ $point['current'] }} objednávok">
                                    <span class="pointer-events-none absolute -top-5 left-1/2 hidden -translate-x-1/2 whitespace-nowrap text-[10px] font-semibold text-slate-700 group-hover:block">{{ $point['current'] }}</span>
                                </div>
                                <div class="group relative w-1/2 max-w-[28px] rounded-t-md bg-slate-400" style="height: {{ $point['previous'] > 0 ? max(3, ($point['previous'] / $maxOrders) * 100) : 0 }}%;" title="{{ $point['previous'] }} objednávok minulý rok">
                                    <span class="pointer-events-none absolute -top-5 left-1/2 hidden -translate-x-1/2 whitespace-nowrap text-[10px] font-semibold text-slate-700 group-hover:block">{{ $point['previous'] }}</span>
                                </div>
                            </div>
                            <span class="text-[10px] font-semibold text-slate-500">{{ $point['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    </div>
</x-app-layout>
