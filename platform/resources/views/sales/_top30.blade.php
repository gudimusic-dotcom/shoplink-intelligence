<section class="sl-card max-w-[1180px] overflow-hidden p-4 sm:p-5">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="sl-kicker text-rose-300">Live product pulse</p>
            <h2 class="sl-section-title mt-1">Top 30 produktov za posledných 30 dní</h2>
            <p class="mt-1 text-sm text-slate-400">{{ $topSevenSortLabel }} · {{ $topSevenFrom->format('d.m.') }} – {{ $topSevenTo->format('d.m.Y') }}</p>
        </div>
        <div class="rounded-full border border-rose-400/20 bg-rose-400/10 px-3 py-1.5 text-xs font-semibold text-rose-200">
            {{ number_format((float) $topSevenProducts->sum('quantity_sum'), 0, ',', ' ') }} predaných kusov
        </div>
    </div>

    <div class="mt-4 flex flex-wrap gap-2">
        @foreach (['quantity' => 'Kusy', 'revenue' => 'Obrat', 'margin' => 'Marža', 'potential' => 'Potenciál'] as $sortKey => $sortLabel)
            <a href="{{ route('sales.top30', ['sort' => $sortKey]) }}" class="{{ $topSevenSort === $sortKey ? 'sl-btn-primary' : 'sl-btn-secondary' }} h-9 px-3 text-xs">{{ $sortLabel }}</a>
        @endforeach
    </div>

    <div class="mt-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">
        @forelse ($topSevenProducts as $rank => $row)
            @php
                $quantity = (float) $row->quantity_sum;
                $width = max(8, round(($quantity / $topSevenMaxQuantity) * 100));
                $change = $row->quantity_change_percent;
            @endphp
            <article class="group relative overflow-hidden rounded-xl border border-slate-700/80 bg-slate-700/45 p-2.5 transition hover:-translate-y-0.5 hover:border-rose-400/50 hover:bg-slate-800">
                <div class="relative aspect-square overflow-hidden rounded-lg bg-slate-900">
                    @if ($row->image_url)
                        <img src="{{ $row->image_url }}" alt="{{ $row->product_name ?? $row->model }}" loading="lazy" class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
                    @else
                        <div class="grid h-full w-full place-items-center text-2xl font-semibold uppercase text-slate-600">{{ substr($row->model, 0, 2) }}</div>
                    @endif
                    <span class="absolute left-2 top-2 grid h-7 w-7 place-items-center rounded-full bg-slate-950/85 text-xs font-bold text-white">{{ $rank + 1 }}</span>
                    @if ($change !== null)
                        <span class="absolute right-2 top-2 rounded-full bg-slate-950/85 px-2 py-1 text-[10px] font-bold {{ $change >= 0 ? 'text-emerald-300' : 'text-rose-300' }}">{{ $change >= 0 ? '+' : '' }}{{ number_format($change, 0, ',', ' ') }}%</span>
                    @endif
                </div>
                <div class="mt-3 min-w-0">
                    <strong class="block truncate text-sm font-semibold text-slate-50" title="{{ $row->product_name ?? $row->model }}">{{ $row->product_name ?? $row->model }}</strong>
                    <span class="mt-0.5 block truncate text-xs text-slate-500">{{ $row->model }}{{ $row->brand ? ' · '.$row->brand : '' }}</span>
                    <div class="mt-3 grid grid-cols-2 gap-2">
                        <div>
                            <strong class="block text-lg font-semibold text-white">{{ number_format($quantity, 0, ',', ' ') }}</strong>
                            <span class="text-[11px] font-medium uppercase tracking-wide text-slate-500">kusov</span>
                        </div>
                        <div class="text-right">
                            <strong class="block text-sm font-semibold text-slate-200">{{ $row->average_price_eur !== null ? number_format((float) $row->average_price_eur, 2, ',', ' ').' €' : '—' }}</strong>
                            <span class="text-[11px] font-medium uppercase tracking-wide text-slate-500">€/ks bez DPH</span>
                        </div>
                    </div>
                    <div class="mt-2 flex items-center justify-between gap-2 text-[11px] font-medium text-slate-400">
                        <span>{{ number_format((int) $row->order_count, 0, ',', ' ') }} obj.</span>
                        <span>{{ $row->margin_rate !== null ? number_format((float) $row->margin_rate, 0, ',', ' ').'% marža' : 'bez marže' }}</span>
                    </div>
                    <div class="mt-1 flex items-center justify-between gap-2 text-[10px] font-medium text-slate-500">
                        <span>{{ $row->net_revenue_eur !== null ? number_format((float) $row->net_revenue_eur, 0, ',', ' ').' € obrat' : 'bez obratu' }}</span>
                        <span>{{ number_format((int) $row->potential_score, 0, ',', ' ') }}/100 potenciál</span>
                    </div>
                    <div class="mt-2 h-1.5 overflow-hidden rounded-full bg-slate-700"><div class="h-full rounded-full bg-gradient-to-r from-rose-500 to-orange-300" style="width: {{ $width }}%"></div></div>
                </div>
            </article>
        @empty
            <div class="rounded-lg bg-slate-800/60 px-4 py-8 text-sm text-slate-300 sm:col-span-2 lg:col-span-3 xl:col-span-5">Za posledných 7 dní nie sú importované objednávkové dáta.</div>
        @endforelse
    </div>
</section>
