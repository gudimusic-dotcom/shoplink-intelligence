<x-app-layout>
    <x-slot name="header">
        <section class="sl-card overflow-hidden">
            <div class="px-5 py-4 sm:px-6">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="sl-kicker">Sales Intelligence</p>
                        <h1 class="mt-2 text-2xl font-semibold tracking-tight text-slate-50">Top 30 produktov</h1>
                        <p class="mt-1 text-sm text-slate-300">Vizuálny prehľad produktov, ktoré sa predávajú najviac za posledných 30 dní.</p>
                    </div>
                    <a href="{{ route('sales.index') }}" class="sl-btn-secondary">Orders Overview</a>
                </div>
            </div>
        </section>
    </x-slot>

    <div class="sl-shell mt-3 space-y-3">
        @include('sales._top30')
    </div>
</x-app-layout>
