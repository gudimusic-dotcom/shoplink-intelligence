<?php

namespace App\Http\Controllers;

use App\Models\TaskCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class TaskCategoryController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:task_categories,name'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ]);

        $baseCode = Str::slug($validated['name'], '_');
        $code = $baseCode !== '' ? $baseCode : 'category';
        $suffix = 1;

        while (TaskCategory::query()->where('code', $code)->exists()) {
            $suffix++;
            $code = "{$baseCode}_{$suffix}";
        }

        TaskCategory::query()->create([
            'code' => $code,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'sort_order' => $validated['sort_order'] ?? (TaskCategory::query()->max('sort_order') ?? 0) + 10,
            'is_active' => true,
        ]);

        return redirect()
            ->route('tasks.index')
            ->with('status', 'Kategória bola vytvorená.');
    }

    public function update(Request $request, TaskCategory $taskCategory): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('task_categories', 'name')->ignore($taskCategory)],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $taskCategory->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'sort_order' => $validated['sort_order'] ?? $taskCategory->sort_order,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('settings.index')
            ->with('status', 'Kategória bola uložená.');
    }

    public function reorder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category_ids' => ['required', 'array'],
            'category_ids.*' => ['required', 'integer', 'exists:task_categories,id'],
        ]);

        collect($validated['category_ids'])
            ->values()
            ->each(fn (int $categoryId, int $index) => TaskCategory::query()
                ->whereKey($categoryId)
                ->update(['sort_order' => ($index + 1) * 10]));

        return response()->json(['status' => 'ok']);
    }

    public function destroy(TaskCategory $taskCategory): RedirectResponse
    {
        $taskCategory->delete();

        return redirect()
            ->route('settings.index')
            ->with('status', 'Kategória bola vymazaná. Úlohy zostali zachované bez kategórie.');
    }
}
