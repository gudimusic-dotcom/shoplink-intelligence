<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskComment;
use App\Models\TaskCategory;
use App\Models\User;
use App\Notifications\NewTaskAssignedNotification;
use App\Notifications\NewTaskCommentNotification;
use App\Services\WebPushService;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{
    public function __construct(
        private readonly WebPushService $webPushService,
    ) {}

    public function index(Request $request): View
    {
        return $this->renderIndex($request);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'task_category_id' => ['required', 'exists:task_categories,id'],
            'priority' => ['required', Rule::in(['normal', 'high'])],
            'assigned_user_id' => ['nullable', 'exists:users,id'],
            'due_at' => ['nullable', 'date'],
            'assign_to_me' => ['nullable', 'boolean'],
        ]);

        if ($request->boolean('assign_to_me')) {
            $validated['assigned_user_id'] = $request->user()->id;
        }

        unset($validated['assign_to_me']);

        $task = Task::query()->create([
            ...$validated,
            'status' => 'new',
            'created_by' => $request->user()->id,
            'source_type' => User::class,
            'source_id' => $request->user()->id,
            'generated_by' => 'manual',
        ]);

        $task->loadMissing(['category', 'assignedUser']);

        if (
            $task->assignedUser &&
            $task->assignedUser->is_active &&
            $task->assignedUser->email_notif_new_task
        ) {
            $task->assignedUser->notify(new NewTaskAssignedNotification($task, $request->user()));
        }

        if ($task->assignedUser && $task->assignedUser->is_active) {
            $this->webPushService->sendNewTaskAssigned($task, $task->assignedUser, $request->user());
        }

        return redirect()
            ->route('tasks.index')
            ->with('status', 'Úloha bola vytvorená.');
    }

    public function edit(Request $request, Task $task): View
    {
        return $this->renderIndex($request, $task);
    }

    public function updatePreferences(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'show_notes' => ['nullable', 'boolean'],
        ]);

        $request->user()->update([
            'task_center_show_notes' => (bool) ($validated['show_notes'] ?? false),
        ]);

        return back()->with('status', 'Zobrazenie Task Center bolo aktualizované.');
    }

    public function update(Request $request, Task $task): RedirectResponse
    {
        $task->loadMissing(['assignedUser', 'generatedByUser']);
        $originalAssignedUserId = $task->assigned_user_id;
        $allowedStatuses = ['new', 'done'];

        if (! in_array($request->input('status'), $allowedStatuses, true)) {
            $request->merge(['status' => 'new']);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'task_category_id' => ['required', 'exists:task_categories,id'],
            'priority' => ['required', Rule::in(['normal', 'high'])],
            'status' => ['required', Rule::in($allowedStatuses)],
            'assigned_user_id' => ['nullable', 'exists:users,id'],
            'due_at' => ['nullable', 'date'],
        ]);

        $task->update($validated);
        $task->loadMissing(['assignedUser', 'generatedByUser']);

        if ($task->assignedUser && $task->assignedUser->is_active) {
            if ((int) $originalAssignedUserId !== (int) $task->assigned_user_id) {
                $this->webPushService->sendNewTaskAssigned($task, $task->assignedUser, $request->user());
            } else {
                $this->webPushService->sendTaskUpdated($task, $task->assignedUser, $request->user());
            }
        }

        if (
            $task->generatedByUser &&
            $task->generatedByUser->is_active &&
            ! $task->generatedByUser->is($request->user()) &&
            (! $task->assignedUser || ! $task->generatedByUser->is($task->assignedUser))
        ) {
            $this->webPushService->sendTaskUpdated($task, $task->generatedByUser, $request->user());
        }

        return redirect()
            ->route('tasks.index')
            ->with('status', 'Úloha bola aktualizovaná.');
    }

    public function updateStatus(Request $request, Task $task): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $task->loadMissing('category');
        $allowedStatuses = ['new', 'done'];

        $validated = $request->validate([
            'status' => ['required', Rule::in($allowedStatuses)],
        ]);

        $task->update([
            'status' => $validated['status'],
        ]);

        $task->load(['category', 'comments.user']);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Stav úlohy bol uložený.',
                'task' => $this->taskSnapshotData($task, $request->user()->id),
            ]);
        }

        return redirect()
            ->route('tasks.index')
            ->with('status', 'Stav úlohy bol aktualizovaný.');
    }

    public function storeComment(Request $request, Task $task): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'body' => ['required', 'string', 'max:4000'],
        ]);

        $comment = $task->comments()->create([
            'user_id' => $request->user()->id,
            'body' => $validated['body'],
        ]);

        $task->loadMissing(['assignedUser', 'generatedByUser']);
        $task->load(['comments.user']);

        if ($task->assignedUser && $task->assignedUser->is_active) {
            $this->webPushService->sendNewTaskComment($task, $task->assignedUser, $request->user());
        }

        if (
            $task->generatedByUser &&
            $task->generatedByUser->is_active &&
            ! $task->generatedByUser->is($request->user()) &&
            (! $task->assignedUser || ! $task->generatedByUser->is($task->assignedUser))
        ) {
            $this->webPushService->sendNewTaskComment($task, $task->generatedByUser, $request->user());
        }

        $this->sendTaskCommentEmails($task, $comment, $request->user());

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Komentár bol pridaný.',
                'comments_html' => $this->renderCommentsHtml($task, $request->user()->id),
            ]);
        }

        return back()->with('status', 'Komentár bol pridaný.');
    }

    private function sendTaskCommentEmails(Task $task, TaskComment $comment, User $author): void
    {
        collect([$task->assignedUser, $task->generatedByUser])
            ->filter()
            ->unique('id')
            ->reject(fn (User $recipient) => $recipient->is($author))
            ->filter(fn (User $recipient) => $recipient->is_active && $recipient->email_notif_task_comment)
            ->each(fn (User $recipient) => $recipient->notify(new NewTaskCommentNotification($task, $comment, $author)));
    }

    public function updateComment(Request $request, Task $task, TaskComment $comment): JsonResponse|RedirectResponse
    {
        abort_unless($comment->task_id === $task->id, 404);
        abort_unless($comment->user_id === $request->user()->id, 403);

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:4000'],
        ]);

        $comment->update([
            'body' => $validated['body'],
        ]);

        $task->load(['comments.user']);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Komentár bol upravený.',
                'comments_html' => $this->renderCommentsHtml($task, $request->user()->id),
            ]);
        }

        return back()->with('status', 'Komentár bol upravený.');
    }

    public function destroyComment(Request $request, Task $task, TaskComment $comment): JsonResponse|RedirectResponse
    {
        abort_unless($comment->task_id === $task->id, 404);
        abort_unless($comment->user_id === $request->user()->id, 403);

        $comment->delete();
        $task->load(['comments.user']);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Komentár bol zmazaný.',
                'comments_html' => $this->renderCommentsHtml($task, $request->user()->id),
            ]);
        }

        return back()->with('status', 'Komentár bol zmazaný.');
    }

    public function destroy(Task $task): RedirectResponse
    {
        $task->delete();

        return redirect()
            ->route('tasks.index')
            ->with('status', 'Úloha bola odstránená.');
    }

    public function snapshot(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:tasks,id'],
        ]);

        $tasks = Task::query()
            ->with(['category', 'comments.user'])
            ->whereIn('id', $validated['ids'])
            ->get()
            ->map(fn (Task $task) => $this->taskSnapshotData($task, $request->user()->id))
            ->values();

        return response()->json([
            'tasks' => $tasks,
        ]);
    }

    private function taskSnapshotData(Task $task, int $viewerId): array
    {
        $dueState = $task->status !== 'done' && $task->due_at
            ? ($task->due_at->isToday() ? 'today' : ($task->due_at->isPast() ? 'overdue' : null))
            : null;

        return [
            'id' => $task->id,
            'title' => $task->title,
            'status' => $task->status,
            'status_label' => $this->statusOptionsForTask($task)[$task->status] ?? $task->status,
            'due_at_label' => $task->due_at?->format('d.m.Y'),
            'due_state' => $dueState,
            'comments_html' => $this->renderCommentsHtml($task, $viewerId),
            'updated_at' => $task->updated_at?->toIso8601String(),
        ];
    }

    private function renderIndex(Request $request, ?Task $selectedTask = null): View
    {
        $search = trim($request->string('q')->toString());
        $statusFilter = $request->string('status')->toString();
        $categoryFilter = $request->integer('category');
        $onlyMine = $request->boolean('mine');
        $showDone = $request->boolean('show_done') || $statusFilter === 'done';
        $showNotes = (bool) $request->user()->task_center_show_notes;
        $statusOptions = $this->statusOptions();
        $priorityOptions = [
            'normal' => 'Normálna',
            'high' => 'Vysoká',
        ];

        $recentlyDoneCutoff = now()->subDays(2);

        $tasks = Task::query()
            ->with(['category', 'assignedUser', 'generatedByUser', 'comments.user'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($statusFilter !== '', fn ($query) => $query->where('status', $statusFilter))
            ->when($statusFilter === '' && ! $showDone, function ($query) use ($recentlyDoneCutoff) {
                $query->where(function ($inner) use ($recentlyDoneCutoff) {
                    $inner->where('status', '!=', 'done')
                        ->orWhere(function ($doneQuery) use ($recentlyDoneCutoff) {
                            $doneQuery->where('status', 'done')
                                ->where('updated_at', '>=', $recentlyDoneCutoff);
                        });
                });
            })
            ->when($categoryFilter > 0, fn ($query) => $query->where('task_category_id', $categoryFilter))
            ->when($onlyMine, fn ($query) => $query->where('assigned_user_id', $request->user()->id))
            ->latest()
            ->get()
            ->each(function (Task $task) {
                $task->setAttribute('ui_priority', $this->normalizedPriority($task->priority));
            });

        $categories = TaskCategory::query()
            ->withCount('tasks')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $taskGroupCategories = TaskCategory::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $users = User::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $taskStats = [
            [
                'label' => 'Aktívne',
                'value' => Task::query()->where('status', '!=', 'done')->count(),
            ],
            [
                'label' => 'Dnes',
                'value' => Task::query()->whereDate('due_at', now()->toDateString())->count(),
            ],
            [
                'label' => 'Nové',
                'value' => Task::query()->where('status', 'new')->count(),
            ],
            [
                'label' => 'Hotové',
                'value' => Task::query()->where('status', 'done')->count(),
            ],
        ];

        $loadedSelectedTask = $selectedTask?->loadMissing(['category', 'assignedUser', 'generatedByUser']);

        if ($loadedSelectedTask) {
            $loadedSelectedTask->loadMissing(['comments.user']);
            $loadedSelectedTask->setAttribute('ui_priority', $this->normalizedPriority($loadedSelectedTask->priority));
        }

        return view('tasks.index', [
            'tasks' => $tasks,
            'categories' => $categories,
            'taskGroupCategories' => $taskGroupCategories,
            'users' => $users,
            'taskStats' => $taskStats,
            'search' => $search,
            'statusFilter' => $statusFilter,
            'categoryFilter' => $categoryFilter,
            'onlyMine' => $onlyMine,
            'showDone' => $showDone,
            'showNotes' => $showNotes,
            'selectedTask' => $loadedSelectedTask,
            'statusOptions' => $statusOptions,
            'priorityOptions' => $priorityOptions,
        ]);
    }

    private function statusOptions(): array
    {
        return [
            'new' => 'Nová',
            'done' => 'Vybavená',
        ];
    }

    private function statusOptionsForTask(Task $task): array
    {
        return $this->statusOptions();
    }

    private function normalizedPriority(?string $priority): string
    {
        return in_array($priority, ['high', 'critical'], true) ? 'high' : 'normal';
    }

    private function renderCommentsHtml(Task $task, int $currentUserId): string
    {
        return view('tasks.partials.comment-thread', [
            'task' => $task,
            'currentUserId' => $currentUserId,
        ])->render();
    }
}
