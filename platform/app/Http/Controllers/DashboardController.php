<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskCategory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $dashboardWindowDays = max(1, min(14, (int) ($user->dashboard_task_window_days ?? 3)));
        $today = now()->startOfDay();
        $windowEnd = $today->copy()->addDays($dashboardWindowDays);

        $todayTasks = Task::query()
            ->with(['category', 'assignedUser', 'generatedByUser', 'source'])
            ->whereIn('status', ['new', 'in_progress', 'blocked'])
            ->where(function ($query): void {
                $query->whereNotNull('due_at');
            })
            ->whereDate('due_at', '<=', $windowEnd->toDateString())
            ->orderByRaw("case when due_at < ? then 0 when due_at = ? then 1 else 2 end", [$today->toDateString(), $today->toDateString()])
            ->orderBy('due_at')
            ->latest('id')
            ->limit(8)
            ->get();

        $taskBuckets = [
            [
                'label' => 'Dnes',
                'value' => Task::query()
                    ->whereDate('due_at', now()->toDateString())
                    ->whereIn('status', ['new', 'in_progress', 'blocked'])
                    ->count(),
                'tone' => 'amber',
            ],
            [
                'label' => 'Čaká',
                'value' => Task::query()->where('status', 'blocked')->count(),
                'tone' => 'red',
            ],
            [
                'label' => 'Bez termínu',
                'value' => Task::query()
                    ->whereNull('due_at')
                    ->whereIn('status', ['new', 'in_progress', 'blocked'])
                    ->count(),
                'tone' => 'blue',
            ],
        ];

        $taskCategories = TaskCategory::query()
            ->withCount('tasks')
            ->where('is_active', true)
            ->orderByDesc('tasks_count')
            ->limit(5)
            ->get();

        $taskStatusLabels = [
            'new' => 'Nová',
            'in_progress' => 'Rozpracovaná',
            'blocked' => 'Čaká',
            'done' => 'Hotová',
        ];

        return view('dashboard', [
            'user' => $user,
            'todayTasks' => $todayTasks,
            'dashboardWindowDays' => $dashboardWindowDays,
            'taskBuckets' => $taskBuckets,
            'taskCategories' => $taskCategories,
            'taskStatusLabels' => $taskStatusLabels,
        ]);
    }
}
