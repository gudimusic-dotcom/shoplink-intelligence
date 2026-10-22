<?php

namespace App\Notifications;

use App\Models\Task;
use App\Models\TaskComment;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewTaskCommentNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Task $task,
        private readonly TaskComment $comment,
        private readonly User $author,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Nová poznámka - '.$this->task->title)
            ->greeting($this->task->title)
            ->line($this->author->name)
            ->line($this->comment->body)
            ->action('Otvoriť úlohu', route('tasks.edit', $this->task))
            ->salutation('');
    }
}
