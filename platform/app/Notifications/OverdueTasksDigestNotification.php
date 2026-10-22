<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class OverdueTasksDigestNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Collection $tasks,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Prehľad oneskorených taskov')
            ->greeting('Ahoj '.$notifiable->name.',')
            ->line('Tu je ranný prehľad taskov po termíne, ktoré ešte nie sú hotové.');

        if ($this->tasks->isEmpty()) {
            return $mail
                ->line('Momentálne nemáš žiadne oneskorené tasky.')
                ->action('Otvoriť Task Center', route('tasks.index', ['mine' => 1]));
        }

        foreach ($this->tasks as $task) {
            $mail->line(sprintf(
                '- %s | %s | %s',
                $task->title,
                $task->due_at?->format('d.m.Y') ?? 'Bez termínu',
                $this->translateStatus($task->status)
            ));
        }

        return $mail
            ->action('Otvoriť Task Center', route('tasks.index', ['mine' => 1]))
            ->line('Ak tieto emaily nechceš dostávať, vypni ich v používateľských nastaveniach.');
    }

    private function translateStatus(string $status): string
    {
        return match ($status) {
            'new' => 'Nová',
            'in_progress' => 'Rozpracovaná',
            'blocked' => 'Čaká',
            default => 'Hotová',
        };
    }
}
