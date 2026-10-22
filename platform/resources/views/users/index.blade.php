<x-app-layout>
    <x-slot name="header">
        <section class="sl-card overflow-hidden">
            <div class="px-5 py-4 sm:px-6">
                <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
                    <div>
                        <p class="sl-kicker">User Management</p>
                        <h1 class="mt-2 text-2xl font-semibold tracking-tight text-slate-50">Používatelia a prístupy</h1>
                        <p class="mt-1 text-sm text-slate-300">Úprava mena, emailu, hesla, aktivity a rolí používateľov.</p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <span class="sl-pill">Používatelia <strong class="text-slate-50">{{ $users->count() }}</strong></span>
                        <span class="sl-pill">Aktívni <strong class="text-slate-50">{{ $users->where('is_active', true)->count() }}</strong></span>
                        <span class="sl-pill">Role <strong class="text-slate-50">{{ $roles->count() }}</strong></span>
                    </div>
                </div>
            </div>
        </section>
    </x-slot>

    <div
        x-data="{ openCreate: @js($openCreate || ($errors->any() && !$selectedUser)) }"
        x-init="$watch('openCreate', value => document.body.classList.toggle('overflow-y-hidden', value))"
        class="sl-shell mt-3 space-y-3"
    >
        @if (session('status'))
            <div class="flash-banner">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any() && ! $openCreate)
            <div class="rounded-xl bg-rose-500/10 px-4 py-3 text-sm text-rose-700">
                <strong class="block font-semibold">Úprava neprebehla.</strong>
                <span>Skontroluj označené údaje a skús to znova.</span>
            </div>
        @endif

        <section class="grid gap-6 {{ $selectedUser ? 'xl:grid-cols-[minmax(0,1fr)_440px]' : '' }}">
            <article class="sl-card overflow-hidden">
                <div class="px-5 py-4">
                    <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <p class="sl-kicker">Directory</p>
                            <h2 class="sl-section-title mt-1">Zoznam používateľov</h2>
                        </div>

                        <button
                            type="button"
                            x-on:click="openCreate = true"
                            class="sl-btn-primary"
                        >
                            Nový používateľ
                        </button>
                    </div>
                </div>

                <div class="hidden grid-cols-[minmax(0,1.6fr)_minmax(0,1fr)_120px_120px] gap-4 px-5 py-2 text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-400 lg:grid">
                    <span>Používateľ</span>
                    <span>Role</span>
                    <span>Tasky</span>
                    <span>Stav</span>
                </div>

                <div>
                    @forelse ($users as $userRow)
                        @php($initials = collect(preg_split('/\s+/', trim($userRow->name)))->filter()->take(2)->map(fn ($part) => strtoupper(substr($part, 0, 1)))->implode(''))
                        @php($roleSummary = $userRow->roles->pluck('name')->filter()->implode(', '))
                        <a href="{{ route('users.edit', $userRow) }}" class="grid gap-3 px-4 py-3 transition hover:bg-slate-700/35 lg:grid-cols-[minmax(0,1.6fr)_minmax(0,1fr)_120px_120px] lg:items-center lg:px-5 {{ $selectedUser?->is($userRow) ? 'bg-slate-50' : '' }}">
                            <div class="flex min-w-0 items-center gap-3">
                                <span class="sl-avatar">{{ $initials !== '' ? $initials : 'U' }}</span>
                                <span class="min-w-0">
                                    <strong class="block truncate text-sm font-semibold text-slate-50">{{ $userRow->name }}</strong>
                                    <small class="block truncate text-xs text-slate-400">{{ $userRow->email }}</small>
                                </span>
                            </div>

                            <div class="text-sm text-slate-300">
                                {{ $roleSummary !== '' ? $roleSummary : 'Bez rolí' }}
                            </div>

                            <div>
                                <span class="sl-status bg-slate-700/70 text-slate-900">{{ $userRow->assigned_tasks_count }} taskov</span>
                            </div>

                            <div>
                                <span class="sl-status {{ $userRow->is_active ? 'bg-emerald-500/15 text-emerald-300' : 'bg-rose-500/15 text-rose-300' }}">
                                    {{ $userRow->is_active ? 'Aktívny' : 'Vypnutý' }}
                                </span>
                            </div>
                        </a>
                    @empty
                        <div class="px-5 py-16 text-center">
                            <strong class="block text-base font-semibold text-slate-50">Zatiaľ neexistuje žiadny používateľ.</strong>
                            <p class="mt-2 text-sm text-slate-300">Používatelia vznikajú cez registračný alebo admin proces.</p>
                        </div>
                    @endforelse
                </div>
            </article>

            @if ($selectedUser)
                <aside class="sl-card overflow-hidden">
                    <div class="px-5 py-4">
                        <p class="sl-kicker">Edit</p>
                        <div class="mt-1 flex items-start justify-between gap-3">
                            <div>
                                <h2 class="sl-section-title">Úprava používateľa</h2>
                                <p class="sl-section-copy mt-1">Heslo vyplň iba vtedy, keď ho chceš zmeniť.</p>
                            </div>
                            <form method="POST" action="{{ route('users.notifications.test', $selectedUser) }}">
                                @csrf
                                <button type="submit" class="sl-btn-secondary !px-3 !py-2 text-xs">
                                    Test email
                                </button>
                            </form>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('users.update', $selectedUser) }}" class="space-y-4 px-5 py-5">
                        @csrf
                        @method('PATCH')

                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-slate-900">Meno</label>
                            <x-ts-input name="name" :value="old('name', $selectedUser->name)" class="ts-field-reset" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-slate-900">Email</label>
                            <x-ts-input name="email" type="email" :value="old('email', $selectedUser->email)" class="ts-field-reset" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-slate-900">Role používateľa</label>
                            <div class="grid gap-2 rounded-xl bg-slate-50 p-3">
                                @foreach ($roles as $role)
                                    <label class="flex items-start gap-3 rounded-lg px-2 py-2 text-sm text-slate-900 transition hover:bg-slate-100">
                                        <input
                                            type="checkbox"
                                            name="roles[]"
                                            value="{{ $role->id }}"
                                            @checked(collect(old('roles', $selectedUser->roles->pluck('id')->all()))->map(fn ($roleId) => (int) $roleId)->contains($role->id))
                                            class="sl-admin-checkbox"
                                        >
                                        <span>
                                            <strong class="block font-semibold">{{ $role->name }}</strong>
                                            <small class="text-slate-400">{{ $role->description ?? $role->code }}</small>
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                            <x-input-error :messages="$errors->get('roles')" class="mt-2" />
                            <x-input-error :messages="$errors->get('roles.*')" class="mt-2" />
                        </div>

                        <div class="sl-checkbox-surface">
                            <label class="flex items-start gap-3 text-sm text-slate-900">
                                <input
                                    type="checkbox"
                                    name="is_active"
                                    value="1"
                                    @checked(old('is_active', $selectedUser->is_active))
                                    @disabled(auth()->user()->is($selectedUser))
                                    class="sl-admin-checkbox"
                                />
                                <span>
                                    <strong class="block font-semibold">Používateľ je aktívny</strong>
                                </span>
                            </label>
                            @if (auth()->user()->is($selectedUser))
                                <p class="mt-2 text-xs text-slate-400">Vlastný účet nie je možné vypnúť.</p>
                            @endif
                        </div>

                        <div class="sl-checkbox-surface space-y-3">
                            <label class="flex items-start gap-3 text-sm text-slate-900">
                                <input
                                    type="checkbox"
                                    name="email_notif_new_task"
                                    value="1"
                                    @checked(old('email_notif_new_task', $selectedUser->email_notif_new_task))
                                    class="sl-admin-checkbox"
                                />
                                <span>
                                    <strong class="block font-semibold">Email pri novom tasku</strong>
                                </span>
                            </label>

                            <label class="flex items-start gap-3 text-sm text-slate-900">
                                <input
                                    type="checkbox"
                                    name="email_notif_overdue_daily"
                                    value="1"
                                    @checked(old('email_notif_overdue_daily', $selectedUser->email_notif_overdue_daily))
                                    class="sl-admin-checkbox"
                                />
                                <span>
                                    <strong class="block font-semibold">Denný email o oneskorených taskoch</strong>
                                    <small class="block text-slate-400">Každý deň o 08:00</small>
                                </span>
                            </label>

                            <label class="flex items-start gap-3 text-sm text-slate-900">
                                <input
                                    type="checkbox"
                                    name="email_notif_task_comment"
                                    value="1"
                                    @checked(old('email_notif_task_comment', $selectedUser->email_notif_task_comment))
                                    class="sl-admin-checkbox"
                                />
                                <span>
                                    <strong class="block font-semibold">Email pri novej poznámke</strong>
                                </span>
                            </label>

                            <label class="flex items-start gap-3 text-sm text-slate-900">
                                <input
                                    type="checkbox"
                                    name="web_push_notif_new_task"
                                    value="1"
                                    @checked(old('web_push_notif_new_task', $selectedUser->web_push_notif_new_task))
                                    class="sl-admin-checkbox"
                                />
                                <span>
                                    <strong class="block font-semibold">Push pri novom tasku</strong>
                                </span>
                            </label>
                        </div>

                        <div class="grid gap-3 sm:grid-cols-2">
                            <div>
                                <label class="mb-1.5 block text-xs font-semibold text-slate-900">Nové heslo</label>
                                <x-ts-input name="password" type="password" class="ts-field-reset" autocomplete="new-password" />
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>

                            <div>
                                <label class="mb-1.5 block text-xs font-semibold text-slate-900">Potvrdenie hesla</label>
                                <x-ts-input name="password_confirmation" type="password" class="ts-field-reset" autocomplete="new-password" />
                            </div>
                        </div>

                        <x-ts-button submit color="red" text="Uložiť zmeny" block />
                    </form>
                </aside>
            @endif
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
                    class="h-full w-full max-w-[560px] overflow-y-auto border-l border-slate-200 bg-white shadow-2xl"
                >
                    <div class="sticky top-0 z-10 border-b border-slate-200 bg-white/95 px-5 py-4 backdrop-blur">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h2 class="sl-section-title">Nový používateľ</h2>
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

                    <form method="POST" action="{{ route('users.store') }}" class="space-y-4 px-5 py-5">
                        @csrf

                        @if ($openCreate && $errors->any())
                            <div class="rounded-xl bg-rose-500/10 px-4 py-3 text-sm text-rose-700">
                                <strong class="block font-semibold">Vytvorenie neprebehlo.</strong>
                                <span>Skontroluj označené údaje a skús to znova.</span>
                            </div>
                        @endif

                        <div>
                            <label class="mb-1.5 block text-sm font-semibold text-slate-900">Meno</label>
                            <x-ts-input name="name" :value="old('name')" class="ts-field-reset" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div>
                            <label class="mb-1.5 block text-sm font-semibold text-slate-900">Email</label>
                            <x-ts-input name="email" type="email" :value="old('email')" class="ts-field-reset" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div>
                            <label class="mb-1.5 block text-sm font-semibold text-slate-900">Role používateľa</label>
                            <div class="grid gap-2 rounded-xl bg-slate-50 p-3">
                                @foreach ($roles as $role)
                                    <label class="flex items-start gap-3 rounded-lg px-2 py-2 text-sm text-slate-900 transition hover:bg-slate-100">
                                        <input
                                            type="checkbox"
                                            name="roles[]"
                                            value="{{ $role->id }}"
                                            @checked(collect(old('roles', []))->map(fn ($roleId) => (int) $roleId)->contains($role->id))
                                            class="sl-admin-checkbox"
                                        >
                                        <span>
                                            <strong class="block font-semibold">{{ $role->name }}</strong>
                                            <small class="text-slate-400">{{ $role->description ?? $role->code }}</small>
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                            <x-input-error :messages="$errors->get('roles')" class="mt-2" />
                            <x-input-error :messages="$errors->get('roles.*')" class="mt-2" />
                        </div>

                        <div class="sl-checkbox-surface">
                            <label class="flex items-start gap-3 text-sm text-slate-900">
                                <input
                                    type="checkbox"
                                    name="is_active"
                                    value="1"
                                    @checked(old('is_active', true))
                                    class="sl-admin-checkbox"
                                />
                                <span>
                                    <strong class="block font-semibold">Používateľ je aktívny</strong>
                                </span>
                            </label>
                        </div>

                        <div class="sl-checkbox-surface space-y-3">
                            <label class="flex items-start gap-3 text-sm text-slate-900">
                                <input
                                    type="checkbox"
                                    name="email_notif_new_task"
                                    value="1"
                                    @checked(old('email_notif_new_task', true))
                                    class="sl-admin-checkbox"
                                />
                                <span>
                                    <strong class="block font-semibold">Email pri novom tasku</strong>
                                </span>
                            </label>

                            <label class="flex items-start gap-3 text-sm text-slate-900">
                                <input
                                    type="checkbox"
                                    name="email_notif_overdue_daily"
                                    value="1"
                                    @checked(old('email_notif_overdue_daily', true))
                                    class="sl-admin-checkbox"
                                />
                                <span>
                                    <strong class="block font-semibold">Denný email o oneskorených taskoch</strong>
                                    <small class="block text-slate-400">Každý deň o 08:00</small>
                                </span>
                            </label>

                            <label class="flex items-start gap-3 text-sm text-slate-900">
                                <input
                                    type="checkbox"
                                    name="email_notif_task_comment"
                                    value="1"
                                    @checked(old('email_notif_task_comment', true))
                                    class="sl-admin-checkbox"
                                />
                                <span>
                                    <strong class="block font-semibold">Email pri novej poznámke</strong>
                                </span>
                            </label>

                            <label class="flex items-start gap-3 text-sm text-slate-900">
                                <input
                                    type="checkbox"
                                    name="web_push_notif_new_task"
                                    value="1"
                                    @checked(old('web_push_notif_new_task', true))
                                    class="sl-admin-checkbox"
                                />
                                <span>
                                    <strong class="block font-semibold">Push pri novom tasku</strong>
                                </span>
                            </label>
                        </div>

                        <div class="grid gap-3 sm:grid-cols-2">
                            <div>
                                <label class="mb-1.5 block text-sm font-semibold text-slate-900">Heslo</label>
                                <x-ts-input name="password" type="password" class="ts-field-reset" autocomplete="new-password" />
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>

                            <div>
                                <label class="mb-1.5 block text-sm font-semibold text-slate-900">Potvrdenie hesla</label>
                                <x-ts-input name="password_confirmation" type="password" class="ts-field-reset" autocomplete="new-password" />
                            </div>
                        </div>

                        <div class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                            <button
                                type="button"
                                x-on:click="openCreate = false"
                                class="sl-btn-secondary"
                            >
                                Zrušiť
                            </button>
                            <x-ts-button submit color="red" text="Vytvoriť používateľa" />
                        </div>
                    </form>
                </aside>
            </div>
        </div>
    </div>
</x-app-layout>
