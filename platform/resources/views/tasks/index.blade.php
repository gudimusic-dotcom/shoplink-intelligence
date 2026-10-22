<x-app-layout>
    <x-slot name="headerShellClass">sl-shell sl-shell-wide pt-3</x-slot>

    <x-slot name="header">
        <section class="sl-card overflow-visible">
                <div class="px-5 py-4 sm:px-6">
                    <div class="flex flex-col gap-3 xl:flex-row xl:items-center xl:justify-between">
                        <div>
                            <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Tasky</h1>
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            @foreach ($taskStats as $stat)
                                <span class="sl-pill">{{ $stat['label'] }} <strong class="text-slate-900">{{ $stat['value'] }}</strong></span>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="bg-slate-50 px-5 py-4 sm:px-6">
                    <div class="grid gap-3 xl:grid-cols-[minmax(0,1fr)_auto] xl:items-end">
                        <form method="GET" action="{{ route('tasks.index') }}" class="grid gap-3 lg:grid-cols-[minmax(0,1.1fr)_repeat(3,minmax(0,0.7fr))]">
                            <div>
                                <label class="sl-form-label">Hľadať</label>
                                <input
                                    type="text"
                                    name="q"
                                    value="{{ $search }}"
                                    placeholder="Názov alebo poznámka"
                                    class="sl-input"
                                >
                            </div>
                            <input type="hidden" name="status" value="{{ $statusFilter }}">
                            <input type="hidden" name="category" value="{{ $categoryFilter ?: '' }}">
                            <input type="hidden" name="show_done" value="{{ $showDone ? '1' : '0' }}">

                            <div>
                                <label class="sl-form-label">Status</label>
                                <details class="sl-status-menu">
                                    <summary class="sl-status-menu-button">
                                        <span>{{ $statusFilter ? ($statusOptions[$statusFilter] ?? $statusFilter) : 'Všetky statusy' }}</span>
                                        <svg class="h-4 w-4 text-slate-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.17l3.71-3.94a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" />
                                        </svg>
                                    </summary>
                                    <div class="sl-status-menu-list">
                                        <a href="{{ route('tasks.index', request()->except('status')) }}" class="sl-status-menu-item">
                                            <span>Všetky statusy</span>
                                            @if (! $statusFilter)
                                                <span class="sl-status-menu-check"></span>
                                            @endif
                                        </a>
                                        @foreach ($statusOptions as $value => $label)
                                            <a href="{{ route('tasks.index', array_merge(request()->except('status'), ['status' => $value])) }}" class="sl-status-menu-item">
                                                <span>{{ $label }}</span>
                                                @if ($statusFilter === $value)
                                                    <span class="sl-status-menu-check"></span>
                                                @endif
                                            </a>
                                        @endforeach
                                    </div>
                                </details>
                            </div>

                            <div>
                                <label class="sl-form-label">Kategória</label>
                                <details class="sl-status-menu">
                                    <summary class="sl-status-menu-button">
                                        <span>{{ $categoryFilter ? ($categories->firstWhere('id', $categoryFilter)?->name ?? 'Všetky kategórie') : 'Všetky kategórie' }}</span>
                                        <svg class="h-4 w-4 text-slate-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.17l3.71-3.94a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" />
                                        </svg>
                                    </summary>
                                    <div class="sl-status-menu-list">
                                        <a href="{{ route('tasks.index', request()->except('category')) }}" class="sl-status-menu-item">
                                            <span>Všetky kategórie</span>
                                            @if (! $categoryFilter)
                                                <span class="sl-status-menu-check"></span>
                                            @endif
                                        </a>
                                        @foreach ($categories as $category)
                                            <a href="{{ route('tasks.index', array_merge(request()->except('category'), ['category' => $category->id])) }}" class="sl-status-menu-item">
                                                <span>{{ $category->name }}</span>
                                                @if ($categoryFilter === $category->id)
                                                    <span class="sl-status-menu-check"></span>
                                                @endif
                                            </a>
                                        @endforeach
                                    </div>
                                </details>
                            </div>

                            <div class="flex items-end">
                                <div class="sl-checkbox-inline">
                                    <x-ts-checkbox
                                        name="mine"
                                        value="1"
                                        label="Iba moje tasky"
                                        color="red"
                                        @checked($onlyMine)
                                        onchange="this.form.submit()"
                                        class="!text-slate-700"
                                    />
                                </div>
                            </div>
                        </form>

                        <div class="flex flex-wrap items-center gap-2">
                            <form method="GET" action="{{ route('tasks.index') }}">
                                <input type="hidden" name="q" value="{{ $search }}">
                                <input type="hidden" name="status" value="{{ $statusFilter }}">
                                <input type="hidden" name="category" value="{{ $categoryFilter ?: '' }}">
                                <input type="hidden" name="mine" value="{{ $onlyMine ? '1' : '0' }}">
                                <input type="hidden" name="show_done" value="{{ $showDone ? '0' : '1' }}">
                                <button type="submit" class="sl-toggle-chip {{ $showDone ? 'sl-toggle-chip-active sl-toggle-chip-done' : '' }}">
                                    Hotové
                                </button>
                            </form>
                            <form method="POST" action="{{ route('tasks.preferences') }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="show_notes" value="{{ $showNotes ? '0' : '1' }}">
                                <button type="submit" class="sl-toggle-chip {{ $showNotes ? 'sl-toggle-chip-active sl-toggle-chip-notes' : '' }}">
                                    Poznámky
                                </button>
                            </form>
                            <button type="button" class="sl-btn-primary" x-on:click="$store.taskCenter.openCreatePanel()" data-task-create-open>
                                Nový task
                            </button>
                        </div>
                    </div>
                </div>
        </section>
    </x-slot>

    <div
        x-data="{
            openCreate: $store.taskCenter.openCreate,
            openEditId: @js($selectedTask?->id),
            openTaskEdit(id) {
                this.openCreate = false;
                this.$store.taskCenter.openCreate = false;
                this.openEditId = id;
            },
            closeTaskEdit() {
                this.openEditId = null;
            },
        }"
        x-init="
            $store.taskCenter.openCreate = @js($errors->any() && ! $selectedTask);
            openCreate = $store.taskCenter.openCreate;
            document.body.classList.toggle('overflow-y-hidden', openCreate || Boolean(openEditId));
            $watch('$store.taskCenter.openCreate', value => {
                openCreate = value;
                if (value) openEditId = null;
                document.body.classList.toggle('overflow-y-hidden', value || Boolean(openEditId));
            });
            $watch('openEditId', value => document.body.classList.toggle('overflow-y-hidden', openCreate || Boolean(value)));
        "
        x-on:keydown.escape.window="openCreate ? $store.taskCenter.closeCreatePanel() : (openEditId ? closeTaskEdit() : null)"
        data-task-show-done="{{ $showDone ? '1' : '0' }}"
        data-task-status-filter="{{ $statusFilter }}"
        class="sl-shell sl-shell-wide mt-3 space-y-3"
    >
        @if (session('status'))
            <div class="flash-banner">
                {{ session('status') }}
            </div>
        @endif
        <div class="sl-toast-stack" data-toast-stack></div>

        <section class="space-y-6">
            <article
                class="sl-card overflow-visible"
                style="
                    --sl-task-owner-col: 70px;
                    --sl-task-due-col: 110px;
                "
            >
                <div class="sl-table-head sl-task-center-head rounded-t-xl !border-b-0">
                    <span>Task</span>
                    <span>Owner</span>
                    <span>Termín</span>
                </div>

                <div>
                    @php
                        $tasksByCategory = $tasks->groupBy(fn ($task) => $task->task_category_id ?: 'uncategorized');
                        $taskGroups = $taskGroupCategories
                            ->filter(fn ($category) => $tasksByCategory->has($category->id))
                            ->map(fn ($category) => [
                                'name' => $category->name,
                                'tone' => 'sl-category-tone-'.(($category->id % 6) + 1),
                                'tasks' => $tasksByCategory->get($category->id),
                            ]);

                        $knownCategoryIds = $taskGroupCategories->pluck('id')->all();
                        $tasksByCategory
                            ->reject(fn ($group, $categoryId) => $categoryId === 'uncategorized' || in_array($categoryId, $knownCategoryIds, true))
                            ->each(function ($orphanTasks) use ($taskGroups) {
                                $firstTask = $orphanTasks->first();

                                $taskGroups->push([
                                    'name' => $firstTask?->category?->name ?? 'Bez kategórie',
                                    'tone' => 'sl-category-neutral',
                                    'tasks' => $orphanTasks,
                                ]);
                            });

                        if ($tasksByCategory->has('uncategorized')) {
                            $taskGroups->push([
                                'name' => 'Bez kategórie',
                                'tone' => 'sl-category-neutral',
                                'tasks' => $tasksByCategory->get('uncategorized'),
                            ]);
                        }
                    @endphp

                    @forelse ($taskGroups as $taskGroup)
                        @php($categoryTasks = $taskGroup['tasks'])
                        <section class="sl-task-category-section mt-2 first:mt-0" data-task-category-section>
                            <div class="sl-task-category-header">
                                <span class="sl-task-category-title">
                                    <span class="sl-task-category-dot"></span>
                                    {{ $taskGroup['name'] }} <span class="sl-task-category-count" data-task-category-count>({{ $categoryTasks->count() }})</span>
                                </span>
                            </div>
                            <div class="sl-task-category-body">

                        @foreach ($categoryTasks as $task)
                        @php($ownerInitials = $task->assignedUser ? collect(preg_split('/\s+/', trim($task->assignedUser->name)))->filter()->take(2)->map(fn ($part) => strtoupper(substr($part, 0, 1)))->implode('') : 'NA')
                        @php($creatorInitials = $task->generatedByUser ? collect(preg_split('/\s+/', trim($task->generatedByUser->name)))->filter()->take(2)->map(fn ($part) => strtoupper(substr($part, 0, 1)))->implode('') : null)
                        @php($showCreatorAvatar = $creatorInitials && $task->created_by && (int) $task->created_by !== (int) $task->assigned_user_id)
                        @php($dueState = $task->status !== 'done' && $task->due_at ? ($task->due_at->isToday() ? 'today' : ($task->due_at->isPast() ? 'overdue' : null)) : null)
                        <div
                            x-on:click="if (! $event.target.closest('[data-task-row-ignore]')) openTaskEdit({{ $task->id }})"
                            data-task-edit-open="{{ $task->id }}"
                            data-task-row-id="{{ $task->id }}"
                            x-bind:class="openEditId === {{ $task->id }} ? 'bg-slate-100' : ''"
                            class="sl-table-row sl-task-center-row cursor-pointer transition hover:bg-slate-50"
                        >
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <button
                                        type="button"
                                        class="sl-order-complete-toggle"
                                        data-task-row-ignore
                                        data-order-complete-toggle
                                        data-task-id="{{ $task->id }}"
                                        data-task-status-action="{{ route('tasks.status', $task) }}"
                                        aria-checked="{{ $task->status === 'done' ? 'true' : 'false' }}"
                                        aria-label="{{ $task->status === 'done' ? 'Označiť úlohu ako novú' : 'Označiť úlohu ako vybavenú' }}"
                                    >
                                        <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2.4" aria-hidden="true">
                                            <path d="m5 10 3 3 7-7"></path>
                                        </svg>
                                    </button>
                                    <a
                                        href="{{ route('tasks.edit', $task) }}"
                                        x-on:click.prevent="openTaskEdit({{ $task->id }})"
                                        data-task-edit-open="{{ $task->id }}"
                                        data-task-title="{{ $task->id }}"
                                        class="truncate text-left text-[15px] font-medium leading-4 text-slate-900 transition hover:text-rose-600"
                                    >
                                        {{ $task->title }}
                                    </a>
                                    @if ($task->ui_priority === 'high')
                                        <span class="sl-priority-compact" aria-label="Vysoká priorita">
                                            ⚠️
                                        </span>
                                    @endif
                                </div>
                                @if ($showNotes && $task->description)
                                    <p class="sl-task-note hidden lg:block">{{ \Illuminate\Support\Str::limit($task->description, 140) }}</p>
                                @endif

                                <div class="mt-2 flex flex-wrap items-center gap-2 text-[12px] text-slate-600 lg:hidden">
                                    <span class="rounded-md bg-slate-100 px-2 py-1 font-medium text-slate-700">
                                        {{ $task->assignedUser?->name ?? 'Nepriradené' }}
                                    </span>
                                    <span data-task-mobile-due="{{ $task->id }}">
                                        @if ($task->due_at)
                                            <span class="rounded-md px-2 py-1 font-medium {{ $dueState === 'overdue' ? 'sl-due-chip sl-due-chip-overdue' : ($dueState === 'today' ? 'sl-due-chip sl-due-chip-today' : 'bg-slate-100 text-slate-700') }}">
                                                {{ $task->due_at->format('d.m.Y') }}
                                            </span>
                                        @else
                                            <span class="sl-date-icon" aria-label="Bez termínu">
                                                <svg viewBox="0 0 20 20" class="h-3.5 w-3.5" fill="currentColor" aria-hidden="true">
                                                    <path fill-rule="evenodd" d="M6 2.75a.75.75 0 0 1 .75.75V4h6.5v-.5a.75.75 0 0 1 1.5 0V4H16a2 2 0 0 1 2 2v8.25a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h1.25v-.5A.75.75 0 0 1 6 2.75ZM3.5 8v6.25a.5.5 0 0 0 .5.5h12a.5.5 0 0 0 .5-.5V8h-13Z" clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                        @endif
                                    </span>
                                </div>
                            </div>

                            <div class="hidden lg:flex items-center justify-start whitespace-nowrap pr-1">
                                <span class="sl-avatar shrink-0 {{ (int) $task->assigned_user_id === (int) auth()->id() ? 'sl-dashboard-task-avatar-self' : '' }}" title="{{ $task->assignedUser?->name ?? 'Nepriradené' }}">{{ $ownerInitials }}</span>
                            </div>

                            <div class="hidden self-center whitespace-nowrap text-[14px] font-medium lg:block">
                                <span data-task-desktop-due="{{ $task->id }}">
                                    @if ($task->due_at)
                                        <span class="sl-dashboard-task-date {{ $dueState === 'overdue' ? 'sl-dashboard-task-date-overdue' : ($dueState === 'today' ? 'sl-dashboard-task-date-today' : 'text-slate-700') }}">
                                            {{ $task->due_at->format('d.m.Y') }}
                                        </span>
                                    @else
                                        <span class="sl-date-icon" aria-label="Bez termínu">
                                            <svg viewBox="0 0 20 20" class="h-4 w-4" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M6 2.75a.75.75 0 0 1 .75.75V4h6.5v-.5a.75.75 0 0 1 1.5 0V4H16a2 2 0 0 1 2 2v8.25a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h1.25v-.5A.75.75 0 0 1 6 2.75ZM3.5 8v6.25a.5.5 0 0 0 .5.5h12a.5.5 0 0 0 .5-.5V8h-13Z" clip-rule="evenodd" />
                                            </svg>
                                        </span>
                                    @endif
                                </span>
                            </div>

                        </div>
                        @endforeach
                            </div>
                        </section>
                    @empty
                        <div class="px-5 py-16 text-center">
                            <strong class="block text-base font-semibold text-slate-900">Zatiaľ tu nie sú žiadne úlohy.</strong>
                            <p class="mt-2 text-sm text-slate-600">Klikni na Nový task a zoznam sa začne plniť v kompaktných riadkoch.</p>
                        </div>
                    @endforelse
                </div>
            </article>

        </section>

        <div
            x-cloak
            x-show="openCreate"
            data-task-create-panel
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
                x-on:click="$store.taskCenter.closeCreatePanel()"
                data-task-create-close
                data-task-create-overlay
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
                    data-task-create-sheet
                    class="sl-task-panel h-full w-full max-w-[620px] overflow-y-auto border-l border-slate-200 bg-white shadow-2xl"
                >
                    <div class="sticky top-0 z-10 border-b border-slate-200 bg-white/96 px-5 py-4 backdrop-blur">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h2 class="sl-section-title">Nový task</h2>
                            </div>

                            <button
                                type="button"
                                x-on:click="$store.taskCenter.closeCreatePanel()"
                                data-task-create-close
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

                    <form
                        method="POST"
                        action="{{ route('tasks.store') }}"
                        x-on:submit="$refs.createDueAtHidden.value = $el.querySelector('.sl-field-shell-date input[type=&quot;text&quot;]')?.value ?? ''"
                        class="space-y-4 px-5 py-5"
                    >
                    @csrf
                    <input type="hidden" name="due_at" value="{{ old('due_at') }}" x-ref="createDueAtHidden">
                    @php($createStatus = 'new')
                    @php($createPriority = old('priority', 'normal') === 'high' ? 'high' : 'normal')
                    @php($defaultCreateCategory = $categories->first())
                    @php($createCategoryId = (int) old('task_category_id', $defaultCreateCategory?->id))
                    @php($createCategory = $categories->firstWhere('id', $createCategoryId))
                    <input type="hidden" name="priority" value="{{ $createPriority }}" x-ref="createPriority">
                    <input type="hidden" name="task_category_id" value="{{ $createCategoryId ?: '' }}" x-ref="createCategory">

                    @if ($errors->any() && ! $selectedTask)
                        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                            <strong class="block font-semibold">Vytvorenie neprebehlo.</strong>
                            <span>{{ $errors->first() ?: 'Skontroluj polia vo formulári a skús to znova.' }}</span>
                        </div>
                    @endif

                    <div>
                        <label class="sl-form-label">Názov tasku</label>
                        <input
                            type="text"
                            name="title"
                            value="{{ old('title') }}"
                            placeholder="Napr. Overiť chýbajúci doklad k reklamácii"
                            class="sl-input sl-panel-input text-[15px]"
                        >
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="sl-form-label">Status</label>
                            <div class="sl-status-menu-button sl-status-menu-button-compact sl-task-status-{{ $createStatus }}">
                                <span class="inline-flex items-center gap-2">
                                    <span class="sl-status-dot"></span>
                                    <span>{{ $statusOptions[$createStatus] ?? 'Nový' }}</span>
                                </span>
                            </div>
                        </div>

                        <div>
                            <label class="sl-form-label">Priorita</label>
                            <details class="sl-status-menu">
                                <summary
                                    class="sl-status-menu-button sl-status-menu-button-compact sl-priority-badge sl-priority-{{ $createPriority }} sl-priority-summary-left"
                                    x-ref="createPrioritySummary"
                                >
                                    <span x-ref="createPriorityLabel">{{ $priorityOptions[$createPriority] ?? $createPriority }}</span>
                                    <svg class="h-4 w-4 text-slate-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.17l3.71-3.94a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" />
                                    </svg>
                                </summary>
                                <div class="sl-status-menu-list">
                                @foreach ($priorityOptions as $value => $label)
                                        <button
                                            type="button"
                                            class="sl-status-menu-item"
                                            x-on:click="
                                                $refs.createPriority.value = @js($value);
                                                $refs.createPriorityLabel.textContent = @js($label);
                                                $refs.createPrioritySummary.className = 'sl-status-menu-button sl-status-menu-button-compact sl-priority-badge sl-priority-{{ $value }} sl-priority-summary-left';
                                                $el.closest('details').open = false;
                                            "
                                        >
                                            <span>{{ $label }}</span>
                                            @if ($createPriority === $value)
                                                <span class="sl-status-menu-check"></span>
                                            @endif
                                        </button>
                                @endforeach
                                </div>
                            </details>
                        </div>

                        <div>
                            <label class="sl-form-label">Termín</label>
                            <div class="sl-field-shell sl-field-shell-date">
                                <x-ts-date
                                    property="due_at_create_picker"
                                    :value="old('due_at')"
                                    format="YYYY-MM-DD"
                                    helpers
                                    class="ts-field-reset"
                                />
                            </div>
                        </div>

                        <div>
                            <label class="sl-form-label">Kategória</label>
                            <details class="sl-status-menu">
                                <summary
                                    class="sl-status-menu-button sl-status-menu-button-compact sl-category-badge {{ $createCategoryId ? 'sl-category-tone-'.(($createCategoryId % 6) + 1) : 'sl-category-neutral' }}"
                                    x-ref="createCategorySummary"
                                >
                                    <span x-ref="createCategoryLabel">{{ $createCategory?->name ?? 'Vyber kategóriu' }}</span>
                                    <svg class="h-4 w-4 text-slate-300" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.17l3.71-3.94a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" />
                                    </svg>
                                </summary>
                                <div class="sl-status-menu-list">
                                @foreach ($categories as $category)
                                        <button
                                            type="button"
                                            class="sl-status-menu-item"
                                            x-on:click="
                                                $refs.createCategory.value = @js($category->id);
                                                $refs.createCategoryLabel.textContent = @js($category->name);
                                                $refs.createCategorySummary.className = 'sl-status-menu-button sl-status-menu-button-compact sl-category-badge sl-category-tone-{{ ($category->id % 6) + 1 }}';
                                                $el.closest('details').open = false;
                                            "
                                        >
                                            <span>{{ $category->name }}</span>
                                            @if ($createCategoryId === $category->id)
                                                <span class="sl-status-menu-check"></span>
                                            @endif
                                        </button>
                                @endforeach
                                </div>
                            </details>
                        </div>

                        <div class="sm:col-span-2">
                            <label class="sl-form-label">Owner</label>
                            <div class="sl-field-shell">
                                <select name="assigned_user_id" class="sl-input sl-panel-input sl-panel-select">
                                    <option value="">Nepriradené</option>
                                    @foreach ($users as $userOption)
                                        <option value="{{ $userOption->id }}" @selected((int) old('assigned_user_id') === $userOption->id)>{{ $userOption->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="sl-form-label">Poznámka</label>
                        <textarea
                            name="description"
                            rows="6"
                            class="sl-input sl-panel-input min-h-[156px] !h-auto py-3"
                        >{{ old('description') }}</textarea>
                    </div>

                    <div>
                        <div class="sl-checkbox-surface">
                            <x-ts-checkbox
                                name="assign_to_me"
                                value="1"
                                label="Priradiť mne"
                                color="red"
                                @checked(old('assign_to_me'))
                                class="!text-slate-100"
                            />
                        </div>
                    </div>

                    <div class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                        <button
                            type="button"
                            x-on:click="$store.taskCenter.closeCreatePanel()"
                            data-task-create-close
                            class="sl-btn-secondary"
                        >
                            Zrušiť
                        </button>
                        <x-ts-button submit color="red" text="Vytvoriť task" />
                    </div>
                </form>
                </aside>
            </div>
        </div>

        @foreach ($tasks as $modalTask)
            @php($useOldTaskValues = $selectedTask?->is($modalTask))
            <div
                x-cloak
                x-show="openEditId === {{ $modalTask->id }}"
                data-task-edit-panel="{{ $modalTask->id }}"
                class="fixed inset-0 z-50"
                style="display: none;"
            >
                <div
                    x-show="openEditId === {{ $modalTask->id }}"
                    x-transition:enter="ease-out duration-200"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-150"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    x-on:click="closeTaskEdit()"
                    data-task-edit-close
                    data-task-edit-overlay
                    class="absolute inset-0 backdrop-blur-[1px]"
                    style="background: rgba(15, 23, 42, 0.28);"
                ></div>

                <div class="absolute inset-y-0 right-0 flex w-full justify-end">
                    <aside
                        x-show="openEditId === {{ $modalTask->id }}"
                        x-transition:enter="transform ease-out duration-250"
                        x-transition:enter-start="translate-x-full"
                        x-transition:enter-end="translate-x-0"
                        x-transition:leave="transform ease-in duration-200"
                        x-transition:leave-start="translate-x-0"
                        x-transition:leave-end="translate-x-full"
                        data-task-edit-sheet
                    class="sl-task-panel h-full w-full max-w-[620px] overflow-y-auto border-l border-slate-200 bg-white shadow-2xl"
                >
                        <div class="sticky top-0 z-10 border-b border-slate-200 bg-white/96 px-5 py-4 backdrop-blur">
                            <div class="flex items-start justify-between gap-4">
                                <div class="min-w-0">
                                    <p class="sl-kicker">Task edit</p>
                                    <h2 class="sl-section-title mt-1 truncate" data-task-panel-title="{{ $modalTask->id }}">{{ $modalTask->title }}</h2>
                                    @if ($modalTask->generatedByUser)
                                        <p class="mt-1 text-xs font-medium text-slate-500">Vytvoril: {{ $modalTask->generatedByUser->name }}</p>
                                    @endif
                                </div>

                                <button
                                    type="button"
                                    x-on:click="closeTaskEdit()"
                                    data-task-edit-close
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

                        <form
                            method="POST"
                            action="{{ route('tasks.update', $modalTask) }}"
                            x-on:submit="$el.querySelector('[name=due_at]').value = $el.querySelector('.sl-field-shell-date input[type=&quot;text&quot;]')?.value ?? ''"
                            class="space-y-4 px-5 py-5"
                        >
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="due_at" value="{{ $useOldTaskValues ? old('due_at', $modalTask->due_at?->format('Y-m-d')) : $modalTask->due_at?->format('Y-m-d') }}">
                            @php($modalStatus = $useOldTaskValues ? old('status', $modalTask->status) : $modalTask->status)
                            @php($modalPriority = ($useOldTaskValues ? old('priority', $modalTask->ui_priority ?? $modalTask->priority) : ($modalTask->ui_priority ?? $modalTask->priority)) === 'high' ? 'high' : 'normal')
                            @php($modalCategoryId = (int) ($useOldTaskValues ? old('task_category_id', $modalTask->task_category_id) : $modalTask->task_category_id))
                            @php($modalCategory = $categories->firstWhere('id', $modalCategoryId))
                            <input type="hidden" name="status" value="{{ $modalStatus }}" x-ref="taskStatus{{ $modalTask->id }}">
                            <input type="hidden" name="priority" value="{{ $modalPriority }}" x-ref="taskPriority{{ $modalTask->id }}">
                            <input type="hidden" name="task_category_id" value="{{ $modalCategoryId }}" x-ref="taskCategory{{ $modalTask->id }}">

                            @if ($useOldTaskValues && $errors->any())
                                <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                                    <strong class="block font-semibold">Úprava neprebehla.</strong>
                                    <span>{{ $errors->first() ?: 'Skontroluj polia vo formulári a skús to znova.' }}</span>
                                </div>
                            @endif

                            <div>
                                <label class="sl-form-label">Názov tasku</label>
                                <input
                                    type="text"
                                    name="title"
                                    value="{{ $useOldTaskValues ? old('title', $modalTask->title) : $modalTask->title }}"
                                    class="sl-input sl-panel-input text-[15px]"
                                >
                                @if ($useOldTaskValues)
                                    <x-input-error :messages="$errors->get('title')" class="mt-2" />
                                @endif
                            </div>

                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="sl-form-label">Status</label>
                                    <button
                                        type="button"
                                        class="sl-order-complete-control"
                                        data-order-complete-toggle
                                        data-task-id="{{ $modalTask->id }}"
                                        data-task-status-action="{{ route('tasks.status', $modalTask) }}"
                                        aria-checked="{{ $modalStatus === 'done' ? 'true' : 'false' }}"
                                        aria-label="{{ $modalStatus === 'done' ? 'Označiť úlohu ako novú' : 'Označiť úlohu ako vybavenú' }}"
                                    >
                                        <span class="sl-order-complete-toggle">
                                            <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2.4" aria-hidden="true">
                                                <path d="m5 10 3 3 7-7"></path>
                                            </svg>
                                        </span>
                                        <span data-order-complete-label>{{ $modalStatus === 'done' ? 'Vybavená' : 'Nová' }}</span>
                                    </button>
                                </div>

                                <div>
                                    <label class="sl-form-label">Priorita</label>
                                    <details class="sl-status-menu">
                                        <summary
                                            class="sl-status-menu-button sl-status-menu-button-compact sl-priority-badge sl-priority-{{ $modalPriority }} sl-priority-summary-left"
                                            x-ref="taskPrioritySummary{{ $modalTask->id }}"
                                        >
                                            <span x-ref="taskPriorityLabel{{ $modalTask->id }}">{{ $priorityOptions[$modalPriority] ?? $modalPriority }}</span>
                                            <svg class="h-4 w-4 text-slate-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.17l3.71-3.94a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" />
                                            </svg>
                                        </summary>
                                        <div class="sl-status-menu-list">
                                        @foreach ($priorityOptions as $value => $label)
                                                <button
                                                    type="button"
                                                    class="sl-status-menu-item"
                                                    x-on:click="
                                                        $refs.taskPriority{{ $modalTask->id }}.value = @js($value);
                                                        $refs.taskPriorityLabel{{ $modalTask->id }}.textContent = @js($label);
                                                        $refs.taskPrioritySummary{{ $modalTask->id }}.className = 'sl-status-menu-button sl-status-menu-button-compact sl-priority-badge sl-priority-{{ $value }} sl-priority-summary-left';
                                                        $el.closest('details').open = false;
                                                    "
                                                >
                                                    <span>{{ $label }}</span>
                                                    @if ($modalPriority === $value)
                                                        <span class="sl-status-menu-check"></span>
                                                    @endif
                                                </button>
                                        @endforeach
                                        </div>
                                    </details>
                                </div>

                                <div>
                                    <label class="sl-form-label">Termín</label>
                                    <div class="sl-field-shell sl-field-shell-date">
                                        <x-ts-date
                                            property="due_at_picker_{{ $modalTask->id }}"
                                            :value="$useOldTaskValues ? old('due_at', $modalTask->due_at?->format('Y-m-d')) : $modalTask->due_at?->format('Y-m-d')"
                                            format="YYYY-MM-DD"
                                            helpers
                                            class="ts-field-reset"
                                        />
                                    </div>
                                </div>

                                <div>
                                    <label class="sl-form-label">Kategória</label>
                                    <details class="sl-status-menu">
                                        <summary
                                            class="sl-status-menu-button sl-status-menu-button-compact sl-category-badge {{ $modalCategoryId ? 'sl-category-tone-'.(($modalCategoryId % 6) + 1) : 'sl-category-neutral' }}"
                                            x-ref="taskCategorySummary{{ $modalTask->id }}"
                                        >
                                            <span x-ref="taskCategoryLabel{{ $modalTask->id }}">{{ $modalCategory?->name ?? 'Vyber kategóriu' }}</span>
                                            <svg class="h-4 w-4 text-slate-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.17l3.71-3.94a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" />
                                            </svg>
                                        </summary>
                                        <div class="sl-status-menu-list">
                                        @foreach ($categories as $category)
                                                <button
                                                    type="button"
                                                    class="sl-status-menu-item"
                                                    x-on:click="
                                                        $refs.taskCategory{{ $modalTask->id }}.value = @js($category->id);
                                                        $refs.taskCategoryLabel{{ $modalTask->id }}.textContent = @js($category->name);
                                                        $refs.taskCategorySummary{{ $modalTask->id }}.className = 'sl-status-menu-button sl-status-menu-button-compact sl-category-badge sl-category-tone-{{ ($category->id % 6) + 1 }}';
                                                        $el.closest('details').open = false;
                                                    "
                                                >
                                                    <span>{{ $category->name }}</span>
                                                    @if ($modalCategoryId === $category->id)
                                                        <span class="sl-status-menu-check"></span>
                                                    @endif
                                                </button>
                                        @endforeach
                                        </div>
                                    </details>
                                </div>

                                <div class="sm:col-span-2">
                                    <label class="sl-form-label">Owner</label>
                                    <div class="sl-field-shell">
                                        <select name="assigned_user_id" class="sl-input sl-panel-input sl-panel-select">
                                            <option value="">Nepriradené</option>
                                            @foreach ($users as $userOption)
                                                <option value="{{ $userOption->id }}" @selected((int) ($useOldTaskValues ? old('assigned_user_id', $modalTask->assigned_user_id) : $modalTask->assigned_user_id) === $userOption->id)>{{ $userOption->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="sl-form-label">Poznámka</label>
                                <textarea
                                    name="description"
                                    rows="6"
                                    class="sl-input sl-panel-input min-h-[156px] !h-auto py-3 text-base"
                                >{{ $useOldTaskValues ? old('description', $modalTask->description) : $modalTask->description }}</textarea>
                            </div>

                            <div class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                                <button
                                    type="submit"
                                    form="task-delete-{{ $modalTask->id }}"
                                    class="sl-btn-secondary !bg-rose-500/12 !text-slate-900 hover:!bg-rose-500/18"
                                >
                                    Zmazať task
                                </button>
                                <button type="button" x-on:click="closeTaskEdit()" data-task-edit-close class="sl-btn-secondary">
                                    Zrušiť
                                </button>
                                <x-ts-button submit color="red" text="Uložiť zmeny" />
                            </div>
                        </form>

                        <section class="space-y-4 border-t border-slate-700/60 px-5 py-5">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <p class="sl-kicker">Komentáre</p>
                                </div>
                            </div>

                            @include('tasks.partials.comment-thread', ['task' => $modalTask, 'currentUserId' => auth()->id()])

                            <form
                                method="POST"
                                action="{{ route('tasks.comments.store', $modalTask) }}"
                                class="space-y-3"
                                data-task-comment-form
                                data-task-id="{{ $modalTask->id }}"
                            >
                                @csrf
                                <textarea
                                    name="body"
                                    rows="3"
                                    class="sl-input sl-panel-input min-h-[96px] !h-auto py-3 text-base"
                                    placeholder="Pridať komentár k postupu práce..."
                                ></textarea>
                                <div class="flex justify-end">
                                    <button type="submit" class="sl-btn-primary">Pridať komentár</button>
                                </div>
                            </form>
                        </section>
                        <form
                            id="task-delete-{{ $modalTask->id }}"
                            method="POST"
                            action="{{ route('tasks.destroy', $modalTask) }}"
                            onsubmit="return confirm('Naozaj chceš zmazať tento task?');"
                            class="hidden"
                        >
                            @csrf
                            @method('DELETE')
                        </form>
                    </aside>
                </div>
            </div>
        @endforeach
    </div>
</x-app-layout>
