<x-app-layout>
    <x-slot name="header">
        <section class="sl-card overflow-hidden">
            <div class="px-5 py-4 sm:px-6">
                <div>
                    <p class="sl-kicker">Morning overview</p>
                    <h1 class="mt-2 text-3xl font-semibold tracking-tight text-slate-50">Dobré ráno, {{ explode(' ', trim($user->name))[0] }}</h1>
                </div>
            </div>
        </section>
    </x-slot>

    <div class="sl-shell mt-3 space-y-3">
        <section class="grid gap-6 xl:grid-cols-[minmax(0,1.3fr)_380px]">
            <article class="sl-card overflow-hidden">
                <div class="px-4 py-3 sm:px-5">
                    <div class="flex items-center justify-between gap-3">
                        <p class="sl-kicker">Next {{ $dashboardWindowDays }} days</p>
                        <a href="{{ route('tasks.index') }}" class="text-sm font-medium text-slate-300 transition hover:text-white">Otvoriť detail</a>
                    </div>
                </div>

                <div class="sl-table-head !grid-cols-[minmax(0,1fr)_48px_136px_108px]">
                    <span>Task</span>
                    <span>Owner</span>
                    <span>Stav</span>
                    <span>Termín</span>
                </div>

                <div>
                    @forelse ($todayTasks as $task)
                        @php($assigneeInitials = $task->assignedUser ? collect(preg_split('/\s+/', trim($task->assignedUser->name)))->filter()->take(2)->map(fn ($part) => strtoupper(substr($part, 0, 1)))->implode('') : 'NA')
                        @php($dueState = $task->due_at ? ($task->due_at->isToday() ? 'today' : ($task->due_at->isPast() ? 'overdue' : null)) : null)
                        <a href="{{ route('tasks.edit', $task) }}" class="sl-dashboard-task-row">
                            <span class="min-w-0 truncate text-[15px] font-semibold leading-5 text-slate-50">{{ $task->title }}</span>
                            <span class="hidden lg:block">
                                <span class="sl-avatar {{ (int) $task->assigned_user_id === (int) $user->id ? 'sl-dashboard-task-avatar-self' : '' }}" title="{{ $task->assignedUser?->name ?? 'Nepriradené' }}">{{ $assigneeInitials }}</span>
                            </span>
                            <span class="sl-status-menu-button sl-status-menu-button-compact sl-task-status-{{ $task->status }}">{{ $taskStatusLabels[$task->status] ?? $task->status }}</span>
                            @if ($task->due_at)
                                <span class="sl-dashboard-task-date {{ $dueState === 'overdue' ? 'sl-dashboard-task-date-overdue' : ($dueState === 'today' ? 'sl-dashboard-task-date-today' : 'text-slate-300') }}">
                                    {{ $task->due_at->format('d.m.Y') }}
                                </span>
                            @else
                                <span class="sl-date-icon hidden lg:inline-flex" aria-label="Bez termínu">
                                    <svg viewBox="0 0 20 20" class="h-3.5 w-3.5" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M6 2.75a.75.75 0 0 1 .75.75V4h6.5v-.5a.75.75 0 0 1 1.5 0V4H16a2 2 0 0 1 2 2v8.25a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h1.25v-.5A.75.75 0 0 1 6 2.75ZM3.5 8v6.25a.5.5 0 0 0 .5.5h12a.5.5 0 0 0 .5-.5V8h-13Z" clip-rule="evenodd" />
                                    </svg>
                                </span>
                            @endif
                        </a>
                    @empty
                        <div class="px-5 py-16 text-center">
                            <strong class="block text-base font-semibold text-slate-50">Zatiaľ nič nečaká v najbližších dňoch.</strong>
                            <p class="mt-2 text-sm text-slate-300">Dashboard ukazuje úlohy s blízkym termínom a tie, ktoré sú po termíne.</p>
                        </div>
                    @endforelse
                </div>
            </article>

            <aside class="space-y-6">
                <article class="sl-card p-5">
                    <p class="sl-kicker">Focus</p>
                    <h2 class="sl-section-title mt-1">Rýchly operatívny stav</h2>

                    <div class="mt-4 space-y-3">
                        @foreach ($taskBuckets as $bucket)
                            <div class="flex items-center justify-between rounded-2xl bg-slate-700/58 px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <span class="h-2.5 w-2.5 rounded-full {{ $bucket['tone'] === 'red' ? 'bg-rose-500' : ($bucket['tone'] === 'amber' ? 'bg-amber-500' : 'bg-slate-400') }}"></span>
                                    <span class="text-sm font-medium text-slate-100">{{ $bucket['label'] }}</span>
                                </div>
                                <strong class="text-sm font-semibold text-slate-50">{{ $bucket['value'] }}</strong>
                            </div>
                        @endforeach
                    </div>
                </article>

                <article class="sl-card p-5">
                    <p class="sl-kicker">Categories</p>
                    <h2 class="sl-section-title mt-1">Najvyťaženejšie kategórie</h2>

                    <div class="mt-4 space-y-3">
                        @foreach ($taskCategories as $category)
                            <div class="space-y-2">
                                <div class="flex items-center justify-between text-sm">
                                    <strong class="text-slate-50">{{ $category->name }}</strong>
                                    <span class="text-slate-300">{{ $category->tasks_count }}</span>
                                </div>
                                <div class="h-2 rounded-full bg-slate-700/55">
                                    <div class="h-2 rounded-full bg-rose-500" style="width: {{ min(100, max(10, $category->tasks_count * 12)) }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </article>
            </aside>
        </section>
    </div>
</x-app-layout>
