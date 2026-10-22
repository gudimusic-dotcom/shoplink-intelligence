<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'name',
    'email',
    'password',
    'is_active',
    'default_role_id',
    'email_notif_new_task',
    'email_notif_task_comment',
    'email_notif_overdue_daily',
    'web_push_notif_new_task',
    'task_center_show_notes',
    'dashboard_task_window_days',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_active' => 'boolean',
            'email_notif_new_task' => 'boolean',
            'email_notif_task_comment' => 'boolean',
            'email_notif_overdue_daily' => 'boolean',
            'web_push_notif_new_task' => 'boolean',
            'task_center_show_notes' => 'boolean',
            'dashboard_task_window_days' => 'integer',
            'password' => 'hashed',
        ];
    }

    public function defaultRole(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'default_role_id');
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class)
            ->withTimestamps();
    }

    public function assignedTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'assigned_user_id');
    }

    public function createdMonitors(): HasMany
    {
        return $this->hasMany(Monitor::class, 'created_by');
    }

    public function taskComments(): HasMany
    {
        return $this->hasMany(TaskComment::class);
    }

    public function pushSubscriptions(): HasMany
    {
        return $this->hasMany(PushSubscription::class);
    }

    public function hasRole(string $code): bool
    {
        return $this->roles->contains('code', $code);
    }

    public function hasAnyRole(array $codes): bool
    {
        return $this->roles->pluck('code')->intersect($codes)->isNotEmpty();
    }

    public function canAccessSalesIntelligence(): bool
    {
        return $this->hasAnyRole(['admin', 'ceo', 'finance']);
    }

    public function canManageUsers(): bool
    {
        return $this->hasRole('admin');
    }
}
