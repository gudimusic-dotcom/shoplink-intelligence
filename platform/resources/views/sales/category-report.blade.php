<x-app-layout>
    <x-slot name="header">
        <section class="sl-card overflow-hidden">
            <div class="px-5 py-4 sm:px-6">
                <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
                    <div>
                        <p class="sl-kicker">Sales Intelligence</p>
                        <h1 class="mt-2 text-2xl font-semibold tracking-tight text-slate-50">Kategórie KPI</h1>
                        <p class="mt-1 text-sm text-slate-300">Kategórie s vysokou koncentráciou obratu a priestorom na rozšírenie sortimentu.</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ route('sales.index', request()->only(['date_from', 'date_to'])) }}" class="sl-btn-secondary">Overview</a>
                        <a href="{{ route('sales.detail', request()->only(['date_from', 'date_to'])) }}" class="sl-btn-secondary">Sales Detail</a>
                    </div>
                </div>
            </div>
        </section>
    </x-slot>

    <div class="sl-shell mt-3 space-y-3">
        <section class="sl-card w-fit max-w-full p-3">
            <form method="GET" action="{{ route('sales.category-report') }}" class="flex flex-wrap items-end gap-2">
                <label class="block w-[174px]">
                    <span class="mb-1.5 block text-xs font-semibold text-slate-300">Od</span>
                    <input type="text" name="date_from" value="{{ $dateFrom }}" placeholder="YYYY-MM-DD" pattern="\d{4}-\d{2}-\d{2}" class="sl-date-input h-11 w-full" autocomplete="off">
                </label>
                <label class="block w-[174px]">
                    <span class="mb-1.5 block text-xs font-semibold text-slate-300">Do</span>
                    <input type="text" name="date_to" value="{{ $dateTo }}" placeholder="YYYY-MM-DD" pattern="\d{4}-\d{2}-\d{2}" class="sl-date-input h-11 w-full" autocomplete="off">
                </label>
                <label class="block w-[180px]">
                    <span class="mb-1.5 block text-xs font-semibold text-slate-300">Úroveň kategórie</span>
                    <select name="category_level" class="h-11 w-full rounded-lg border border-slate-600 bg-slate-800 px-3 text-sm text-slate-100">
                        <option value="2" @selected($categoryLevel === '2')>2. úroveň</option>
                        <option value="3" @selected($categoryLevel === '3')>3. úroveň</option>
                        <option value="leaf" @selected($categoryLevel === 'leaf')>Koncová kategória</option>
                    </select>
                </label>
                <label class="block w-[180px]">
                    <span class="mb-1.5 block text-xs font-semibold text-slate-300">Predajný kanál</span>
                    <select name="channel" class="h-11 w-full rounded-lg border border-slate-600 bg-slate-800 px-3 text-sm text-slate-100">
                        <option value="all" @selected($channel === 'all')>Všetko</option>
                        <option value="marketplace" @selected($channel === 'marketplace')>Len marketplace</option>
                        <option value="own_web" @selected($channel === 'own_web')>Bez marketplace</option>
                    </select>
                </label>
                <button type="submit" class="sl-btn-primary h-11 px-4 text-sm">Filtrovať</button>
                <a href="{{ route('sales.category-report') }}" class="sl-btn-secondary h-11 px-4 text-sm">Reset</a>
                @if ($dateBounds?->first_date)
                    <span class="sl-pill h-11">{{ $dateBounds->first_date }} - {{ $dateBounds->last_date }}</span>
                @endif
            </form>
        </section>

        <section class="sl-card overflow-hidden">
            <div class="px-5 py-4">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="sl-kicker">Sortiment expansion</p>
                        <h2 class="sl-section-title mt-1">Kategórie podľa 6 KPI</h2>
                    </div>
                    <p class="max-w-xl text-xs leading-5 text-slate-400">„80 % tržieb“ ukazuje, aké percento všetkých katalógových SKU v kategórii vytvorí prvých 80 % obratu. Nižšie percento znamená vyššiu koncentráciu predaja v úspešných produktoch.</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-[1120px] w-full whitespace-nowrap text-sm">
                    <thead class="text-left text-[11px] font-semibold uppercase tracking-[0.12em] text-slate-400">
                        <tr class="bg-slate-900/35">
                            <th class="px-5 py-3">Kategória</th>
                            <th class="px-4 py-3 text-right">Tržby</th>
                            <th class="px-4 py-3 text-right">Zisk</th>
                            <th class="px-4 py-3 text-right">Marža</th>
                            <th class="px-4 py-3 text-right">Počet SKU</th>
                            <th class="px-4 py-3 text-right">Tržby / SKU</th>
                            <th class="px-4 py-3 text-right">SKU → 80 % tržieb</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/40">
                        @forelse ($categoryRows as $row)
                            <tr class="transition hover:bg-slate-700/35">
                                <td class="px-5 py-3 font-semibold text-slate-50">{{ $row->category_name }}</td>
                                <td class="px-4 py-3 text-right font-semibold text-slate-100">{{ number_format($row->revenue_eur, 0, ',', ' ') }} €</td>
                                <td class="px-4 py-3 text-right font-semibold {{ $row->profit_eur < 0 ? 'text-rose-300' : 'text-emerald-400' }}">{{ number_format($row->profit_eur, 0, ',', ' ') }} €</td>
                                <td class="px-4 py-3 text-right text-slate-200">{{ $row->margin_rate === null ? '—' : number_format($row->margin_rate, 1, ',', ' ').'%' }}</td>
                                <td class="px-4 py-3 text-right text-slate-200">{{ number_format($row->active_sku_count, 0, ',', ' ') }}</td>
                                <td class="px-4 py-3 text-right text-slate-200">{{ number_format($row->revenue_per_sku, 0, ',', ' ') }} €</td>
                                <td class="px-4 py-3 text-right">
                                    @if ($row->sku_to_80_percent === null)
                                        <span class="text-slate-500">—</span>
                                    @else
                                        <strong class="text-amber-400">{{ number_format($row->sku_to_80_percent, 1, ',', ' ') }}%</strong>
                                        <span class="block text-xs text-slate-500">{{ number_format($row->sku_to_80_count, 0, ',', ' ') }} SKU</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-5 py-8 text-sm text-slate-300">Pre zvolené obdobie alebo importované kategórie nie sú dostupné dáta.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</x-app-layout>
