<x-app-layout>
    <style>
        #settings-rates-form input[name^="rates["][name$="[rate_to_eur]"] {
            width: 10ch !important;
            max-width: 10ch !important;
        }

    </style>

    <x-slot name="header">
        <section class="sl-card overflow-hidden">
            <div class="px-5 py-4 sm:px-6">
                <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
                    <div>
                        <p class="sl-kicker">Settings</p>
                        <h1 class="mt-2 text-2xl font-semibold tracking-tight text-slate-50">Nastavenia systému</h1>
                        <p class="mt-1 text-sm text-slate-300">Konfigurácia kurzov, importov a systémových pravidiel.</p>
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
                <strong class="block font-semibold">Nastavenia sa nepodarilo uložiť.</strong>
                <span>Skontroluj zadané hodnoty.</span>
            </div>
        @endif

        <section class="sl-settings-category-editor" style="display:grid;grid-template-columns:minmax(0,1.55fr) minmax(260px,1fr) minmax(220px,.8fr);gap:12px;align-items:start;">
            <article
                class="sl-card p-5"
                x-data="{
                    dragId: null,
                    savingOrder: false,
                    savedOrder: false,
                    reorderUrl: @js(route('task-categories.reorder')),
                    token: @js(csrf_token()),
                    startDrag(event, id) {
                        this.dragId = id;
                        event.dataTransfer.effectAllowed = 'move';
                        event.currentTarget.classList.add('opacity-55');
                    },
                    endDrag(event) {
                        event.currentTarget.setAttribute('draggable', 'false');
                        event.currentTarget.classList.remove('opacity-55');
                    },
                    dropCategory(event) {
                        const dragged = this.$refs.categoryList.querySelector(`[data-category-id='${this.dragId}']`);
                        const target = event.currentTarget;

                        if (! dragged || dragged === target) {
                            return;
                        }

                        const targetBox = target.getBoundingClientRect();
                        const placeAfter = event.clientY > targetBox.top + (targetBox.height / 2);
                        target.parentNode.insertBefore(dragged, placeAfter ? target.nextSibling : target);
                        this.persistOrder();
                    },
                    async persistOrder() {
                        this.savingOrder = true;
                        this.savedOrder = false;

                        const categoryIds = Array.from(this.$refs.categoryList.querySelectorAll('[data-category-id]'))
                            .map((row) => row.dataset.categoryId);

                        Array.from(this.$refs.categoryList.querySelectorAll('[data-category-row]'))
                            .forEach((row, index) => {
                                row.querySelector('[name=sort_order]').value = (index + 1) * 10;
                            });

                        const response = await fetch(this.reorderUrl, {
                            method: 'PATCH',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.token,
                            },
                            body: JSON.stringify({ category_ids: categoryIds }),
                        });

                        if (! response.ok) {
                            window.location.reload();
                            return;
                        }

                        this.savingOrder = false;
                        this.savedOrder = true;
                        setTimeout(() => this.savedOrder = false, 1800);
                    },
                }"
            >
                <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="sl-kicker">Task Center</p>
                        <h2 class="sl-section-title mt-1">Kategórie taskov</h2>
                        <p class="sl-section-copy mt-1">Potiahni kategóriu za úchyt a Task list použije rovnaké poradie.</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <span x-show="savingOrder" x-cloak class="sl-pill">Ukladám poradie...</span>
                        <span x-show="savedOrder" x-cloak class="sl-pill text-emerald-200">Poradie uložené</span>
                        <span class="sl-pill">{{ $taskCategories->count() }} kategórií</span>
                    </div>
                </div>

                <div class="mt-4 grid gap-2 px-2 text-[10px] font-bold uppercase tracking-[0.14em] text-slate-500" style="grid-template-columns: 30px minmax(0, 1fr) 44px 64px 64px;">
                    <span></span>
                    <span>Názov</span>
                    <span class="text-center">Aktívna</span>
                    <span></span>
                    <span></span>
                </div>

                <div x-ref="categoryList" class="mt-1 space-y-1.5">
                    @foreach ($taskCategories as $category)
                        <form
                            method="POST"
                            action="{{ route('task-categories.update', $category) }}"
                            data-category-row
                            data-category-id="{{ $category->id }}"
                            draggable="false"
                            x-on:dragstart="startDrag($event, {{ $category->id }})"
                            x-on:dragend="endDrag($event)"
                            x-on:dragover.prevent
                            x-on:drop.prevent="dropCategory($event)"
                            class="grid items-center gap-2 rounded-lg bg-slate-50 p-2 transition hover:bg-slate-100"
                            style="grid-template-columns: 30px minmax(0, 1fr) 44px 64px 64px;"
                        >
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="sort_order" value="{{ $category->sort_order }}">

                            <button
                                type="button"
                                x-on:mousedown="$el.closest('[data-category-row]').setAttribute('draggable', 'true')"
                                class="flex h-8 w-8 cursor-grab items-center justify-center rounded-md bg-white text-slate-500 transition hover:bg-slate-100 hover:text-slate-900 active:cursor-grabbing border border-slate-200"
                                aria-label="Zmeniť poradie kategórie"
                                title="Potiahni pre zmenu poradia"
                            >
                                <svg viewBox="0 0 20 20" class="h-4 w-4" fill="currentColor" aria-hidden="true">
                                    <path d="M7 4.5A1.5 1.5 0 1 1 4 4.5 1.5 1.5 0 0 1 7 4.5Zm0 5.5A1.5 1.5 0 1 1 4 10a1.5 1.5 0 0 1 3 0Zm-1.5 7A1.5 1.5 0 1 0 5.5 14a1.5 1.5 0 0 0 0 3Zm10-11A1.5 1.5 0 1 0 15.5 3a1.5 1.5 0 0 0 0 3Zm1.5 4A1.5 1.5 0 1 1 14 10a1.5 1.5 0 0 1 3 0Zm-1.5 7A1.5 1.5 0 1 0 15.5 14a1.5 1.5 0 0 0 0 3Z" />
                                </svg>
                            </button>

                            <div>
                                <label class="sr-only">Názov</label>
                                <x-ts-input name="name" :value="$category->name" class="ts-field-reset" />
                            </div>

                            <input type="hidden" name="description" value="{{ $category->description }}">

                            <label class="inline-flex items-center justify-center gap-3 rounded-md bg-white px-2 py-1.5 text-sm font-semibold text-slate-700 border border-slate-200">
                                <span class="sr-only">Aktívna</span>
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" value="1" @checked($category->is_active) class="h-4 w-4 rounded border-slate-500 bg-slate-900 text-rose-500 focus:ring-rose-500/40">
                            </label>

                            <button type="submit" class="sl-btn-secondary h-9 justify-center">
                                Uložiť
                            </button>

                            <button type="submit" class="sl-btn-secondary h-9 justify-center !border-rose-200 !text-rose-600 hover:!bg-rose-50" onclick="if (!confirm('Naozaj vymazať túto kategóriu? Úlohy zostanú bez kategórie.')) return false; this.form.action='{{ route('task-categories.destroy', $category) }}'; this.form.querySelector('[name=_method]').value='DELETE';">
                                Zmazať
                            </button>
                        </form>
                    @endforeach
                </div>
            </article>

            <aside class="space-y-3">
                @include('settings.partials.task-center')
            </aside>

            <aside class="space-y-3">
                <article class="sl-card p-4">
                <form method="POST" action="{{ route('task-categories.store') }}" class="grid gap-2">
                    @csrf
                    <div>
                        <label class="sl-form-label">Názov kategórie taskov</label>
                        <x-ts-input name="name" :value="old('name')" placeholder="Napr. Marketplace" class="ts-field-reset" />
                    </div>

                    <div>
                        <label class="sl-form-label">Poradie kategórie taskov</label>
                        <x-ts-input name="sort_order" :value="old('sort_order', ($taskCategories->max('sort_order') ?? 0) + 10)" inputmode="numeric" class="ts-field-reset" />
                    </div>

                    <input type="hidden" name="description" value="">

                    <div>
                        <button type="submit" class="sl-btn-primary">Pridať kategóriu taskov</button>
                    </div>
                </form>
                </article>
            </aside>
        </section>

        <section class="grid gap-3">
            <article class="sl-card p-5">
                <p class="sl-kicker">Exchange rates</p>
                <h2 class="sl-section-title mt-1">Kurzy voči EUR</h2>
                <p class="sl-section-copy mt-1">Zadaj kurz ako hodnotu lokálnej meny za 1 EUR. Napríklad Kč 24.2 znamená 1 EUR = 24.2 Kč.</p>

                <form id="settings-rates-form" method="POST" action="{{ route('settings.rates.update') }}" class="mt-4 flex flex-wrap items-center gap-2">
                    @csrf
                    @method('PATCH')
                    @foreach ($rates as $index => $rate)
                        <div class="inline-flex w-fit items-center gap-2 rounded-lg bg-slate-50 p-2">
                            <input type="hidden" name="rates[{{ $index }}][currency]" value="{{ $rate->currency }}">
                            <x-ts-input name="rates[{{ $index }}][rate_to_eur]" :value="old('rates.'.$index.'.rate_to_eur', $rate->rate_to_eur !== null ? number_format((float) $rate->rate_to_eur, 2, '.', '') : null)" step="0.01" inputmode="decimal" class="ts-field-reset !w-[10ch]" />
                            <span class="sl-pill justify-center">{{ $rate->currency }}</span>
                        </div>
                    @endforeach
                    <div class="basis-full flex justify-end">
                        <button type="submit" class="sl-btn-primary">Uložiť a prepočítať objednávky</button>
                    </div>
                </form>
            </article>

            <article class="sl-card p-5">
                <div>
                    <p class="sl-kicker">Product Stats</p>
                    <h2 class="sl-section-title mt-1">Dodávatelia a objednávanie</h2>
                    <p class="sl-section-copy mt-1">Linky dodávateľov, cieľové dni, objednávny tovar a skladové feedy na jednom mieste.</p>
                    <form id="settings-hurtel-stock-import" method="POST" action="{{ route('product-stats.import-hurtel-stock') }}" enctype="multipart/form-data">
                        @csrf
                    </form>
                    <form id="settings-fdd-stock-import" method="POST" action="{{ route('product-stats.import-fdd-stock') }}" enctype="multipart/form-data">
                        @csrf
                    </form>
                    <form id="settings-fdd-incoming-import" method="POST" action="{{ route('product-stats.import-fdd-incoming') }}" enctype="multipart/form-data">
                        @csrf
                    </form>
                    @foreach ($supplierPortalLinks as $deleteLink)
                        @php($deleteSupplierKey = mb_strtolower($deleteLink['supplier']))
                        @if ($deleteSupplierKey === 'fdd' && $deleteLink['incoming_count'] > 0)
                            <form id="clear-fdd-incoming" method="POST" action="{{ route('product-stats.clear-fdd-incoming') }}" class="hidden">
                                @csrf
                                @method('DELETE')
                            </form>
                        @endif
                        @if (in_array($deleteSupplierKey, ['fdd', 'hurtel'], true) && $deleteLink['stock_count'] > 0)
                            <form id="clear-stock-{{ $loop->index }}" method="POST" action="{{ route('product-stats.clear-supplier-stock', ['supplier' => $deleteLink['supplier']]) }}" class="hidden">
                                @csrf
                                @method('DELETE')
                            </form>
                        @endif
                    @endforeach
                    <form method="POST" action="{{ route('settings.product-stats-suppliers.update') }}" class="mt-3 grid gap-2">
                        @csrf
                        @method('PATCH')
                        <div class="flex items-center justify-between gap-3 rounded-lg bg-slate-100 px-3 py-2">
                            <span class="sl-form-label !mb-0">Aktívni dodávatelia</span>
                            <button type="button" x-data="{ showAllSuppliers: false }" @click="$dispatch('toggle-all-suppliers')" class="sl-btn-primary !px-3 !py-2">
                                Zobraziť všetkých dodávateľov ({{ $supplierPortalLinks->where('has_data', false)->count() }})
                            </button>
                        </div>
                        <div x-data="{ showAllSuppliers: false }" @toggle-all-suppliers.window="showAllSuppliers = !showAllSuppliers">
                        @foreach ($supplierPortalLinks as $link)
                            @php($supplierKey = mb_strtolower($link['supplier']))
                            <div x-show="showAllSuppliers || {{ $link['has_data'] ? 'true' : 'false' }}" class="rounded-xl border border-slate-200 bg-white p-3">
                                <input type="hidden" name="suppliers[{{ $loop->index }}][supplier_name]" value="{{ $link['supplier'] }}">
                                <div class="grid gap-3" style="grid-template-columns: 500px minmax(0, 1fr);">
                                    <div class="space-y-3 rounded-lg bg-slate-50 p-3">
                                        <div class="block rounded-lg px-3 py-2" style="background-color:#0f172a;color:#ffffff;">
                                            <strong class="text-lg font-semibold leading-6" style="color:#ffffff;">{{ $link['supplier'] }}</strong>
                                        </div>
                                        <div>
                                            <label class="sl-form-label" for="supplier-portal-{{ $loop->index }}">Dodávateľ URL</label>
                                            <x-ts-input id="supplier-portal-{{ $loop->index }}" name="suppliers[{{ $loop->index }}][portal_url_template]" :value="old('suppliers.'.$loop->index.'.portal_url_template', $link['portal_url_template'])" placeholder="https://...{code}" class="ts-field-reset" />
                                        </div>
                                        <div>
                                            <label class="sl-form-label" for="supplier-days-{{ $loop->index }}">Cieľ dní</label>
                                            <x-ts-input id="supplier-days-{{ $loop->index }}" name="suppliers[{{ $loop->index }}][target_coverage_days]" :value="old('suppliers.'.$loop->index.'.target_coverage_days', $link['target_coverage_days'])" inputmode="numeric" class="ts-field-reset" />
                                        </div>
                                    </div>
                                    <div class="grid gap-3 px-1">
                                        <div class="border-b border-slate-200 pb-3">
                                            <div class="mb-3">
                                            <div class="flex flex-wrap items-center justify-between gap-2">
                                                <span class="text-sm font-semibold text-slate-950">Objednaný tovar</span>
                                                @if ($supplierKey === 'fdd')
                                                    <div class="flex flex-wrap items-center justify-end gap-2">
                                                        <span class="text-sm text-slate-950">{{ $link['incoming_count'] > 0 ? "{$link['incoming_count']} / {$link['incoming_units']} ks" : 'Bez feedu' }}{{ $link['incoming_checked_at'] ? ' · '.$link['incoming_checked_at'] : '' }}</span>
                                                        @if ($link['incoming_count'] > 0)
                                                            <button type="submit" form="clear-fdd-incoming" class="sl-btn-secondary !px-2 !py-1.5 !text-rose-600" onclick="return confirm('Naozaj vymazať importovaný objednaný tovar FDD?')">Vymazať dáta</button>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        @if ($supplierKey === 'fdd')
                                            <div class="grid items-end gap-3 sm:grid-cols-[minmax(0,1fr)_auto]">
                                                <div>
                                                    <label class="sl-form-label" for="supplier-incoming-{{ $loop->index }}">Feed URL</label>
                                                    <x-ts-input id="supplier-incoming-{{ $loop->index }}" name="suppliers[{{ $loop->index }}][incoming_feed_url]" :value="old('suppliers.'.$loop->index.'.incoming_feed_url', $link['incoming_feed_url'])" placeholder="URL objednateľného CSV" class="ts-field-reset" />
                                                </div>
                                                <div class="flex flex-wrap gap-1">
                                                    <button type="submit" formaction="{{ route('product-stats.import-fdd-incoming-url') }}" name="import_incoming_supplier_index" value="{{ $loop->index }}" class="sl-btn-primary !px-3 !py-2">Import URL</button>
                                                    <label class="sl-btn-primary cursor-pointer !px-3 !py-2">
                                                        Import CSV
                                                        <input form="settings-fdd-incoming-import" type="file" name="file" accept=".csv,.txt" class="sr-only" required onchange="this.form.submit()">
                                                    </label>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-sm text-slate-950">Import pre tohto dodávateľa zatiaľ nie je zapracovaný.</span>
                                        @endif
                                        </div>
                                        <div class="pt-0">
                                            <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                                                <span class="text-sm font-semibold text-slate-950">Sklad dodávateľa</span>
                                                <div class="flex flex-wrap items-center justify-end gap-2">
                                                    @if ($link['stock_count'] > 0)
                                                        <span class="text-sm text-slate-950">{{ $link['stock_count'] }} ks{{ $link['stock_checked_at'] ? ' · '.$link['stock_checked_at'] : '' }}</span>
                                                        @if (in_array($supplierKey, ['fdd', 'hurtel'], true))
                                                            <button type="submit" form="clear-stock-{{ $loop->index }}" class="sl-btn-secondary !px-2 !py-1.5 !text-rose-600" onclick="return confirm('Naozaj vymazať importovaný sklad dodávateľa {{ $link['supplier'] }}?')">Vymazať dáta</button>
                                                        @endif
                                                    @else
                                                        <span class="text-sm text-slate-950">Bez feedu</span>
                                                    @endif
                                                </div>
                                            </div>
                                            @if ($supplierKey === 'hurtel' || $supplierKey === 'fdd')
                                                <div class="grid items-end gap-3 sm:grid-cols-[minmax(0,1fr)_auto]">
                                                    <div>
                                                        <label class="sl-form-label" for="supplier-stock-{{ $loop->index }}">Feed URL</label>
                                                        <x-ts-input id="supplier-stock-{{ $loop->index }}" name="suppliers[{{ $loop->index }}][stock_feed_url]" :value="old('suppliers.'.$loop->index.'.stock_feed_url', $link['stock_feed_url'])" placeholder="URL XML, ZIP alebo CSV" class="ts-field-reset" />
                                                    </div>
                                                    <div class="flex flex-wrap gap-1">
                                                        <button type="submit" formaction="{{ route('product-stats.import-supplier-stock-url') }}" name="import_stock_supplier_index" value="{{ $loop->index }}" class="sl-btn-primary !px-3 !py-2">Import URL</button>
                                                        <label class="sl-btn-primary cursor-pointer !px-3 !py-2">
                                                            Import {{ $supplierKey === 'hurtel' ? 'XML' : 'ZIP/CSV' }}
                                                            <input form="{{ $supplierKey === 'hurtel' ? 'settings-hurtel-stock-import' : 'settings-fdd-stock-import' }}" type="file" name="file" accept="{{ $supplierKey === 'hurtel' ? '.xml,text/xml' : '.zip,.csv,.txt' }}" class="sr-only" required onchange="this.form.submit()">
                                                        </label>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-sm text-slate-950">Import pre tohto dodávateľa zatiaľ nie je zapracovaný.</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="sl-btn-primary">Uložiť dodávateľov</button>
                        </div>
                    </form>
                </div>
            </article>

        </section>
    </div>
</x-app-layout>
