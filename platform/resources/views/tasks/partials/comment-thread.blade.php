<div class="space-y-3" data-task-comments-list="{{ $task->id }}">
    @include('tasks.partials.comments-list', ['task' => $task, 'currentUserId' => $currentUserId])
</div>
