<?php

namespace App\Notifications;

use App\Models\Task;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewTaskAssignedNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Task $task,
        private readonly User $creator,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Nová úloha - '.$this->task->title)
            ->greeting($this->task->title)
            ->line('Zadal: '.$this->creator->name);

        if ($this->task->due_at) {
            $mail->line('Termín: '.$this->task->due_at->format('d.m.Y'));
        }

        if (filled($this->task->description)) {
            $mail->line($this->task->description);
        }

        return $mail
            ->action('Otvoriť úlohu', route('tasks.edit', $this->task))
            ->salutation('');
    }
}
