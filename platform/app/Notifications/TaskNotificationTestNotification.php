<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskNotificationTestNotification extends Notification
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Test email notifikácií')
            ->greeting('Ahoj '.$notifiable->name.',')
            ->line('Toto je testovací email pre task notifikácie.')
            ->line('Ak si dostal tento email, mail nastavenie funguje správne.')
            ->action('Otvoriť Task Center', route('tasks.index'));
    }
}
