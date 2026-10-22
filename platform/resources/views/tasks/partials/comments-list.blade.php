@forelse ($task->comments as $comment)
    @php($commentInitials = collect(preg_split('/\s+/', trim($comment->user->name)))->filter()->take(2)->map(fn ($part) => strtoupper(substr($part, 0, 1)))->implode(''))
    <article class="sl-comment-card" data-task-comment="{{ $comment->id }}">
        <div class="flex items-start gap-3">
            <span class="sl-comment-avatar">{{ $commentInitials }}</span>
            <div class="min-w-0 flex-1">
                <div class="flex flex-wrap items-center gap-x-2 gap-y-1">
                    <strong class="text-sm font-semibold text-slate-100">{{ $comment->user->name }}</strong>
                    <span class="text-xs text-slate-500">{{ $comment->updated_at->format('d.m.Y H:i') }}</span>
                </div>
                <p class="mt-1 whitespace-pre-line text-sm leading-6 text-slate-200" data-comment-body>{{ $comment->body }}</p>

                @if ($comment->user_id === $currentUserId)
                    <div class="mt-2 flex flex-wrap items-center gap-3">
                        <button type="button" class="text-xs font-semibold text-slate-400 transition hover:text-slate-100" data-comment-edit-toggle>
                            Upraviť
                        </button>
                        <form
                            method="POST"
                            action="{{ route('tasks.comments.destroy', [$task, $comment]) }}"
                            data-task-comment-delete-form
                            class="inline"
                        >
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs font-semibold text-rose-300 transition hover:text-rose-200">
                                Zmazať
                            </button>
                        </form>
                    </div>

                    <form
                        method="POST"
                        action="{{ route('tasks.comments.update', [$task, $comment]) }}"
                        class="mt-3 hidden space-y-2"
                        data-task-comment-edit-form
                    >
                        @csrf
                        @method('PATCH')
                        <textarea
                            name="body"
                            rows="3"
                            class="sl-input sl-panel-input min-h-[92px] !h-auto py-3 text-base"
                        >{{ $comment->body }}</textarea>
                        <div class="flex flex-wrap justify-end gap-2">
                            <button type="button" class="sl-btn-secondary" data-comment-edit-cancel>Zrušiť</button>
                            <button type="submit" class="sl-btn-primary">Uložiť komentár</button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </article>
@empty
    <div class="rounded-xl bg-slate-800/45 px-4 py-4 text-sm text-slate-400">
        Zatiaľ bez komentárov.
    </div>
@endforelse
