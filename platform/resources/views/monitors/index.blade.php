<x-app-layout>
    <x-slot name="header">
        <section class="sl-card overflow-hidden">
            <div class="px-5 py-4 sm:px-6">
                <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
                    <div>
                        <h1 class="text-2xl font-semibold tracking-tight text-slate-50">Monitors</h1>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        @foreach ($stats as $stat)
                            <span class="sl-pill">
                                {{ $stat['label'] }}
                                <strong class="text-slate-50">{{ $stat['value'] }}</strong>
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    </x-slot>

    <div
        x-data="{ openCreate: @js($errors->any() && !$selectedMonitor), openEdit: @js((bool) $selectedMonitor) }"
        x-init="$watch('openCreate', value => document.body.classList.toggle('overflow-y-hidden', value || openEdit)); $watch('openEdit', value => document.body.classList.toggle('overflow-y-hidden', value || openCreate))"
        class="sl-shell mt-3 space-y-3"
    >
        @if (session('status'))
            <div class="flash-banner">
                {{ session('status') }}
            </div>
        @endif

        @if ($testResult)
            <section class="sl-card overflow-hidden">
                <div class="px-5 py-4">
                    <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                        <div>
                            <h2 class="sl-section-title">Posledný test monitoru</h2>
                            <p class="mt-1 text-[15px] {{ $testResult['ok'] ? 'text-emerald-300' : 'text-amber-300' }}">
                                {{ $testResult['message'] }}
                            </p>
                        </div>
                        @if (!empty($testResult['checked_at']))
                            <div class="text-[15px] text-slate-400">
                                {{ $testResult['checked_at'] }}
                            </div>
                        @endif
                    </div>

                    <div class="mt-4 grid gap-3 md:grid-cols-2 xl:grid-cols-4">
                        <div class="rounded-xl bg-slate-700/45 px-4 py-3">
                            <div class="text-xs uppercase tracking-[0.16em] text-slate-400">Engine</div>
                            <div class="mt-2 text-[15px] font-semibold text-slate-100">{{ $engineOptions[$testResult['engine_type'] ?? 'simple'] ?? ($testResult['engine_type'] ?? 'Simple fetch') }}</div>
                        </div>
                        <div class="rounded-xl bg-slate-700/45 px-4 py-3">
                            <div class="text-xs uppercase tracking-[0.16em] text-slate-400">HTTP</div>
                            <div class="mt-2 text-[15px] font-semibold text-slate-100">{{ $testResult['status_code'] ?? 'Chyba' }}</div>
                        </div>
                        <div class="rounded-xl bg-slate-700/45 px-4 py-3">
                            <div class="text-xs uppercase tracking-[0.16em] text-slate-400">Challenge</div>
                            <div class="mt-2 text-[15px] font-semibold text-slate-100">{{ $testResult['challenge'] ?? 'Žiadna' }}</div>
                        </div>
                        <div class="rounded-xl bg-slate-700/45 px-4 py-3 md:col-span-2">
                            <div class="text-xs uppercase tracking-[0.16em] text-slate-400">Title</div>
                            <div class="mt-2 text-[15px] font-semibold text-slate-100">{{ $testResult['title'] ?? 'Nenašiel sa title' }}</div>
                        </div>
                        <div class="rounded-xl bg-slate-700/45 px-4 py-3 md:col-span-2 xl:col-span-1">
                            <div class="text-xs uppercase tracking-[0.16em] text-slate-400">HTML size</div>
                            <div class="mt-2 text-[15px] font-semibold text-slate-100">{{ isset($testResult['html_bytes']) ? number_format($testResult['html_bytes'], 0, ',', ' ') . ' B' : 'Nedostupné' }}</div>
                        </div>
                    </div>

                    <div class="mt-4 rounded-xl bg-slate-700/45 px-4 py-4">
                        <div class="text-xs uppercase tracking-[0.16em] text-slate-400">Nájdené ceny</div>
                        @if (!empty($testResult['price_candidates']))
                            <div class="mt-3 flex flex-wrap gap-2">
                                @foreach ($testResult['price_candidates'] as $candidate)
                                    <span class="sl-status bg-sky-500/14 text-sky-200">{{ $candidate }}</span>
                                @endforeach
                            </div>
                        @else
                            <p class="mt-2 text-[15px] text-slate-300">V surovom HTML sa nenašla žiadna jasná cena.</p>
                        @endif
                    </div>
                </div>
            </section>
        @endif

        <section class="space-y-6">
            <article class="sl-card overflow-hidden">
                <div class="px-5 py-4">
                    <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                        <h2 class="sl-section-title">Zoznam monitorov</h2>

                        <div class="flex flex-wrap items-center gap-2">
                            <button
                                type="button"
                                x-on:click="openCreate = true"
                                class="sl-btn-primary"
                            >
                                Nový monitor
                            </button>
                        </div>
                    </div>
                </div>

                <div class="hidden grid-cols-[minmax(0,2.1fr)_130px_140px_140px_210px] gap-4 px-5 py-2 text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-400 lg:grid">
                    <span>Monitor</span>
                    <span>Engine</span>
                    <span>Posledný test</span>
                    <span>Kontrola</span>
                    <span>Akcie</span>
                </div>

                <div>
                    @forelse ($monitors as $monitor)
                        <div class="grid gap-3 px-4 py-3 transition hover:bg-slate-700/35 lg:grid-cols-[minmax(0,2.1fr)_130px_140px_140px_210px] lg:items-center lg:px-5 {{ $selectedMonitor?->is($monitor) ? 'bg-slate-700/35' : '' }}">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <strong class="truncate text-[15px] font-semibold text-slate-50">{{ $monitor->name }}</strong>
                                    <span class="sl-status {{ $monitor->type === 'price_watch' ? 'bg-sky-500/14 text-sky-200' : 'bg-slate-700/70 text-slate-100' }}">
                                        {{ $typeOptions[$monitor->type] ?? $monitor->type }}
                                    </span>
                                    <span class="sl-status {{ $monitor->is_active ? 'bg-emerald-500/14 text-emerald-200' : 'bg-amber-500/14 text-amber-200' }}">
                                        {{ $monitor->is_active ? 'Aktívny' : 'Pauza' }}
                                    </span>
                                </div>
                                <p class="mt-1 truncate text-[15px] text-slate-300">{{ $monitor->target_url }}</p>
                                <div class="mt-1 flex flex-wrap items-center gap-2 text-[15px] text-slate-400">
                                    <span>{{ $monitor->marketplace ?: 'Bez marketplace' }}</span>
                                    <span>•</span>
                                    <span>Každých {{ $monitor->check_interval_hours }}h</span>
                                </div>
                                @if ($monitor->notes)
                                    <p class="mt-1 text-[15px] text-slate-400">{{ \Illuminate\Support\Str::limit($monitor->notes, 130) }}</p>
                                @endif
                                <div class="mt-2 flex flex-wrap items-center gap-2 lg:hidden">
                                    <span class="sl-status {{ $monitor->engine_type === 'browser' ? 'bg-violet-500/14 text-violet-200' : 'bg-slate-700/70 text-slate-100' }}">
                                        {{ $engineOptions[$monitor->engine_type] ?? $monitor->engine_type }}
                                    </span>
                                </div>
                            </div>

                            <div class="hidden lg:block">
                                <span class="sl-status {{ $monitor->engine_type === 'browser' ? 'bg-violet-500/14 text-violet-200' : 'bg-slate-700/70 text-slate-100' }}">
                                    {{ $engineOptions[$monitor->engine_type] ?? $monitor->engine_type }}
                                </span>
                            </div>

                            <div class="hidden text-[15px] text-slate-300 lg:block">
                                @php
                                    $testTone = match ($monitor->last_test_status) {
                                        'pending' => 'bg-sky-500/14 text-sky-200',
                                        'running' => 'bg-cyan-500/14 text-cyan-200',
                                        'ok' => 'bg-emerald-500/14 text-emerald-200',
                                        'partial' => 'bg-amber-500/14 text-amber-200',
                                        'blocked' => 'bg-violet-500/14 text-violet-200',
                                        'failed' => 'bg-rose-500/14 text-rose-200',
                                        default => 'bg-slate-700/70 text-slate-100',
                                    };
                                    $testLabel = match ($monitor->last_test_status) {
                                        'pending' => 'Queue',
                                        'running' => 'Running',
                                        'ok' => 'OK',
                                        'partial' => 'Čiastočný',
                                        'blocked' => 'Blocked',
                                        'failed' => 'Chyba',
                                        default => 'Bez testu',
                                    };
                                @endphp
                                <span class="sl-status {{ $testTone }}">{{ $testLabel }}</span>
                            </div>

                            <div class="hidden text-[15px] text-slate-300 lg:block">
                                {{ $monitor->last_checked_at?->format('d.m.Y H:i') ?? 'Neoverené' }}
                            </div>

                            <div class="flex items-center justify-between gap-2 lg:justify-end">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('monitors.edit', $monitor) }}" class="sl-inline-action">
                                        Upraviť
                                    </a>
                                    <form method="POST" action="{{ route('monitors.test', $monitor) }}">
                                        @csrf
                                        <button type="submit" class="sl-inline-action">
                                            Test
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('monitors.toggle', $monitor) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="sl-inline-action">
                                            {{ $monitor->is_active ? 'Pauza' : 'Zapnúť' }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="px-5 py-16 text-center">
                            <strong class="block text-base font-semibold text-slate-50">Zatiaľ neexistuje žiadny monitor.</strong>
                            <p class="mt-2 text-[15px] text-slate-300">Prvý krok je pridať sledovanie ceny produktu alebo marketplace zmeny.</p>
                            <button
                                type="button"
                                x-on:click="openCreate = true"
                                class="sl-btn-primary mt-5"
                            >
                                Pridať prvý monitor
                            </button>
                        </div>
                    @endforelse
                </div>
            </article>
        </section>

        <div
            x-cloak
            x-show="openCreate"
            class="fixed inset-0 z-50"
            style="display: none;"
        >
            <div
                x-show="openCreate"
                x-transition:enter="ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                x-on:click="openCreate = false"
                class="absolute inset-0 backdrop-blur-[1px]"
                style="background: rgba(15, 23, 42, 0.28);"
            ></div>

            <div class="absolute inset-y-0 right-0 flex w-full justify-end">
                <aside
                    x-show="openCreate"
                    x-transition:enter="transform ease-out duration-250"
                    x-transition:enter-start="translate-x-full"
                    x-transition:enter-end="translate-x-0"
                    x-transition:leave="transform ease-in duration-200"
                    x-transition:leave-start="translate-x-0"
                    x-transition:leave-end="translate-x-full"
                    class="h-full w-full max-w-[560px] overflow-y-auto border-l border-slate-700/60 bg-slate-900 shadow-2xl"
                >
                    <div class="sticky top-0 z-10 border-b border-slate-700/60 bg-slate-900/96 px-5 py-4 backdrop-blur">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h2 class="sl-section-title">Pridať monitor</h2>
                            </div>

                            <button
                                type="button"
                                x-on:click="openCreate = false"
                                class="sl-icon-btn !h-9 !w-9 !rounded-lg"
                                aria-label="Zavrieť"
                            >
                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M6 6l12 12"></path>
                                    <path d="M18 6l-12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('monitors.store') }}" class="space-y-4 px-5 py-5">
                        @csrf

                        @if ($errors->any())
                            <div class="rounded-xl bg-rose-500/10 px-4 py-3 text-sm text-rose-200">
                                <strong class="block font-semibold">Monitor sa nepodarilo uložiť.</strong>
                                <span>Skontroluj polia vo formulári a skús to znova.</span>
                            </div>
                        @endif

                        <div>
                            <label class="sl-form-label">Názov monitoru</label>
                            <input type="text" name="name" value="{{ old('name') }}" class="sl-input" placeholder="Napr. Alza iPhone 16 price watch" />
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="sl-form-label">Typ</label>
                            <select name="type" class="sl-select">
                                @foreach ($typeOptions as $value => $label)
                                    <option value="{{ $value }}" @selected(old('type', 'price_watch') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="sl-form-label">Engine</label>
                            <select name="engine_type" class="sl-select">
                                @foreach ($engineOptions as $value => $label)
                                    <option value="{{ $value }}" @selected(old('engine_type', 'simple') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="sm:col-span-2">
                            <label class="sl-form-label">Marketplace</label>
                            <select name="marketplace" class="sl-select">
                                <option value="">Vyber marketplace</option>
                                    @foreach ($marketplaceOptions as $option)
                                        <option value="{{ $option }}" @selected(old('marketplace') === $option)>{{ $option }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="sl-form-label">URL adresa</label>
                            <input type="url" name="target_url" value="{{ old('target_url') }}" class="sl-input" placeholder="https://www.alza.sk/..." />
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="sl-form-label">Kontrola každých</label>
                                <div class="flex items-center gap-2">
                                    <input type="number" min="1" max="168" step="1" name="check_interval_hours" value="{{ old('check_interval_hours', 6) }}" class="sl-input" />
                                    <span class="text-sm text-slate-300">hod.</span>
                                </div>
                            </div>

                            <div>
                                <label class="sl-form-label">Email pre alert</label>
                                <input type="email" name="alert_email" value="{{ old('alert_email', auth()->user()->email) }}" class="sl-input" placeholder="name@mobilego.sk" />
                            </div>
                        </div>

                        <div>
                            <label class="sl-form-label">Poznámka</label>
                            <textarea name="notes" rows="4" class="sl-input h-auto py-3" placeholder="Čo sa má sledovať alebo čo spraviť po zmene ceny">{{ old('notes') }}</textarea>
                        </div>

                        <div class="space-y-3 rounded-xl bg-slate-700/55 px-4 py-4">
                            <label class="flex items-start gap-3 text-sm text-slate-100">
                                <input type="checkbox" name="should_create_task" value="1" @checked(old('should_create_task')) class="mt-1 rounded border-slate-500 bg-slate-800 text-rose-500 focus:ring-rose-500" />
                                <span>
                                    <strong class="block font-semibold">Vytvoriť task po triggri</strong>
                                    <small class="text-slate-400">Okrem alert emailu sa môže neskôr automaticky vytvoriť aj úloha.</small>
                                </span>
                            </label>
                        </div>

                        <div class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                            <button
                                type="button"
                                x-on:click="openCreate = false"
                                class="sl-btn-secondary"
                            >
                                Zrušiť
                            </button>
                            <x-ts-button submit color="red" text="Vytvoriť monitor" />
                        </div>
                    </form>
                </aside>
            </div>
        </div>

        @if ($selectedMonitor)
            <div
                x-cloak
                x-show="openEdit"
                class="fixed inset-0 z-50"
                style="display: none;"
            >
                <div
                    x-show="openEdit"
                    x-transition:enter="ease-out duration-200"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-150"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    x-on:click="window.location.href='{{ route('monitors.index') }}'"
                    class="absolute inset-0 backdrop-blur-[1px]"
                    style="background: rgba(15, 23, 42, 0.28);"
                ></div>

                <div class="absolute inset-y-0 right-0 flex w-full justify-end">
                    <aside
                        x-show="openEdit"
                        x-transition:enter="transform ease-out duration-250"
                        x-transition:enter-start="translate-x-full"
                        x-transition:enter-end="translate-x-0"
                        x-transition:leave="transform ease-in duration-200"
                        x-transition:leave-start="translate-x-0"
                        x-transition:leave-end="translate-x-full"
                        class="h-full w-full max-w-[560px] overflow-y-auto border-l border-slate-700/60 bg-slate-900 shadow-2xl"
                    >
                        <div class="sticky top-0 z-10 border-b border-slate-700/60 bg-slate-900/96 px-5 py-4 backdrop-blur">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <h2 class="sl-section-title">Upraviť monitor</h2>
                                </div>

                                <a
                                    href="{{ route('monitors.index') }}"
                                    class="sl-icon-btn !h-9 !w-9 !rounded-lg"
                                    aria-label="Zavrieť"
                                >
                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="M6 6l12 12"></path>
                                        <path d="M18 6l-12 12"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('monitors.update', $selectedMonitor) }}" class="space-y-4 px-5 py-5">
                            @csrf
                            @method('PATCH')

                            <div>
                                <label class="sl-form-label">Názov monitoru</label>
                                <input type="text" name="name" value="{{ old('name', $selectedMonitor->name) }}" class="sl-input" />
                            </div>

                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="sl-form-label">Typ</label>
                                    <select name="type" class="sl-select">
                                        @foreach ($typeOptions as $value => $label)
                                            <option value="{{ $value }}" @selected(old('type', $selectedMonitor->type) === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="sl-form-label">Engine</label>
                                    <select name="engine_type" class="sl-select">
                                        @foreach ($engineOptions as $value => $label)
                                            <option value="{{ $value }}" @selected(old('engine_type', $selectedMonitor->engine_type) === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="sm:col-span-2">
                                    <label class="sl-form-label">Marketplace</label>
                                    <select name="marketplace" class="sl-select">
                                        <option value="">Vyber marketplace</option>
                                        @foreach ($marketplaceOptions as $option)
                                            <option value="{{ $option }}" @selected(old('marketplace', $selectedMonitor->marketplace) === $option)>{{ $option }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="sl-form-label">URL adresa</label>
                                <input type="url" name="target_url" value="{{ old('target_url', $selectedMonitor->target_url) }}" class="sl-input" />
                            </div>

                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="sl-form-label">Kontrola každých</label>
                                    <div class="flex items-center gap-2">
                                        <input type="number" min="1" max="168" step="1" name="check_interval_hours" value="{{ old('check_interval_hours', $selectedMonitor->check_interval_hours) }}" class="sl-input" />
                                        <span class="text-sm text-slate-300">hod.</span>
                                    </div>
                                </div>

                                <div>
                                    <label class="sl-form-label">Email pre alert</label>
                                    <input type="email" name="alert_email" value="{{ old('alert_email', $selectedMonitor->alert_email) }}" class="sl-input" />
                                </div>
                            </div>

                            <div>
                                <label class="sl-form-label">Poznámka</label>
                                <textarea name="notes" rows="4" class="sl-input h-auto py-3">{{ old('notes', $selectedMonitor->notes) }}</textarea>
                            </div>

                            <div class="space-y-3 rounded-xl bg-slate-700/55 px-4 py-4">
                                <label class="flex items-start gap-3 text-sm text-slate-100">
                                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $selectedMonitor->is_active)) class="mt-1 rounded border-slate-500 bg-slate-800 text-rose-500 focus:ring-rose-500" />
                                    <span>
                                        <strong class="block font-semibold">Monitor je aktívny</strong>
                                    </span>
                                </label>

                                <label class="flex items-start gap-3 text-sm text-slate-100">
                                    <input type="checkbox" name="should_create_task" value="1" @checked(old('should_create_task', $selectedMonitor->should_create_task)) class="mt-1 rounded border-slate-500 bg-slate-800 text-rose-500 focus:ring-rose-500" />
                                    <span>
                                        <strong class="block font-semibold">Vytvoriť task po triggri</strong>
                                    </span>
                                </label>
                            </div>

                            <div class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                                <a href="{{ route('monitors.index') }}" class="sl-btn-secondary">
                                    Zrušiť
                                </a>
                                <x-ts-button submit color="red" text="Uložiť zmeny" />
                            </div>
                        </form>
                    </aside>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
