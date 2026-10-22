<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\OverdueTasksDigestNotification;
use Illuminate\Console\Command;

class SendOverdueTaskDigestCommand extends Command
{
    protected $signature = 'tasks:send-overdue-digest';

    protected $description = 'Send daily overdue task digest emails to users.';

    public function handle(): int
    {
        User::query()
            ->where('is_active', true)
            ->where('email_notif_overdue_daily', true)
            ->whereHas('assignedTasks', function ($query): void {
                $query
                    ->whereDate('due_at', '<', now()->toDateString())
                    ->whereIn('status', ['new', 'in_progress', 'blocked']);
            })
            ->with(['assignedTasks' => function ($query): void {
                $query
                    ->with('category')
                    ->whereDate('due_at', '<', now()->toDateString())
                    ->whereIn('status', ['new', 'in_progress', 'blocked'])
                    ->orderBy('due_at');
            }])
            ->chunkById(100, function ($users): void {
                foreach ($users as $user) {
                    if ($user->assignedTasks->isEmpty()) {
                        continue;
                    }

                    $user->notify(new OverdueTasksDigestNotification($user->assignedTasks));
                }
            });

        return self::SUCCESS;
    }
}
