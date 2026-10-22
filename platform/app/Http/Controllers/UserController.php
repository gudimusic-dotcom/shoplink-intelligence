<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Notifications\TaskNotificationTestNotification;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    private function authorizeManageUsers(): void
    {
        abort_unless(auth()->user()?->canManageUsers(), 403);
    }

    public function index(): View
    {
        $this->authorizeManageUsers();

        return $this->renderIndex();
    }

    public function edit(User $user): View
    {
        $this->authorizeManageUsers();

        return $this->renderIndex($user);
    }

    public function create(): View
    {
        $this->authorizeManageUsers();

        return $this->renderIndex(null, true);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeManageUsers();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')],
            'default_role_id' => ['nullable', 'integer', 'exists:roles,id'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['exists:roles,id'],
            'is_active' => ['nullable', 'boolean'],
            'email_notif_new_task' => ['nullable', 'boolean'],
            'email_notif_task_comment' => ['nullable', 'boolean'],
            'email_notif_overdue_daily' => ['nullable', 'boolean'],
            'web_push_notif_new_task' => ['nullable', 'boolean'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'email.unique' => 'Používateľ s týmto emailom už existuje.',
        ]);

        $roleIds = collect($validated['roles'] ?? [])
            ->when(
                filled($validated['default_role_id'] ?? null),
                fn ($roles) => $roles->push((int) $validated['default_role_id'])
            )
            ->map(fn ($roleId) => (int) $roleId)
            ->unique()
            ->values();

        $user = User::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'default_role_id' => $validated['default_role_id'] ?? null,
            'is_active' => $request->boolean('is_active', true),
            'email_notif_new_task' => $request->boolean('email_notif_new_task', true),
            'email_notif_task_comment' => $request->boolean('email_notif_task_comment', true),
            'email_notif_overdue_daily' => $request->boolean('email_notif_overdue_daily', true),
            'web_push_notif_new_task' => $request->boolean('web_push_notif_new_task', true),
        ]);

        $user->roles()->sync($roleIds->all());

        return redirect()
            ->route('users.edit', $user)
            ->with('status', 'Používateľ bol vytvorený.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->authorizeManageUsers();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'default_role_id' => ['nullable', 'integer', 'exists:roles,id'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['exists:roles,id'],
            'is_active' => ['nullable', 'boolean'],
            'email_notif_new_task' => ['nullable', 'boolean'],
            'email_notif_task_comment' => ['nullable', 'boolean'],
            'email_notif_overdue_daily' => ['nullable', 'boolean'],
            'web_push_notif_new_task' => ['nullable', 'boolean'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ], [
            'email.unique' => 'Používateľ s týmto emailom už existuje.',
        ]);

        $roleIds = collect($validated['roles'] ?? [])
            ->when(
                filled($validated['default_role_id'] ?? null),
                fn ($roles) => $roles->push((int) $validated['default_role_id'])
            )
            ->map(fn ($roleId) => (int) $roleId)
            ->unique()
            ->values();

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'default_role_id' => $validated['default_role_id'] ?? null,
            'is_active' => $request->user()->is($user) ? true : $request->boolean('is_active'),
            'email_notif_new_task' => $request->boolean('email_notif_new_task', true),
            'email_notif_task_comment' => $request->boolean('email_notif_task_comment', true),
            'email_notif_overdue_daily' => $request->boolean('email_notif_overdue_daily', true),
            'web_push_notif_new_task' => $request->boolean('web_push_notif_new_task', true),
        ];

        if ($request->filled('password')) {
            $updateData['password'] = $validated['password'];
        }

        $user->update($updateData);
        $user->roles()->sync($roleIds->all());

        return redirect()
            ->route('users.edit', $user)
            ->with('status', 'Používateľ bol aktualizovaný.');
    }

    public function sendNotificationTest(User $user): RedirectResponse
    {
        $this->authorizeManageUsers();

        $user->notify(new TaskNotificationTestNotification());

        return redirect()
            ->route('users.edit', $user)
            ->with('status', 'Test email bol odoslaný.');
    }

    private function renderIndex(?User $selectedUser = null, bool $openCreate = false): View
    {
        $users = User::query()
            ->with(['roles'])
            ->withCount('assignedTasks')
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->get();

        $roles = Role::query()
            ->orderBy('name')
            ->get();

        $selectedUser?->loadMissing(['roles']);

        return view('users.index', [
            'users' => $users,
            'roles' => $roles,
            'selectedUser' => $selectedUser,
            'openCreate' => $openCreate,
        ]);
    }
}
