<?php

namespace App\Services;

use App\Models\PushSubscription;
use App\Models\Task;
use App\Models\User;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;
use Throwable;

class WebPushService
{
    public function sendNewTaskAssigned(Task $task, User $recipient, User $creator): void
    {
        if (! $recipient->web_push_notif_new_task) {
            return;
        }

        $this->sendToUser($recipient, [
            'title' => $task->title,
            'body' => 'Nová úloha · '.$this->firstName($creator),
            'icon' => url('/icons/shoplink-icon-192.svg'),
            'badge' => url('/icons/shoplink-badge.svg'),
            'tag' => 'task-'.$task->id,
            'url' => route('tasks.edit', $task),
            'data' => [
                'url' => route('tasks.edit', $task),
                'task_id' => $task->id,
                'created_by' => $creator->name,
                'due_at' => $task->due_at?->format('d.m.Y'),
            ],
        ]);
    }

    public function sendTaskUpdated(Task $task, User $recipient, User $actor): void
    {
        if (! $recipient->web_push_notif_new_task || $recipient->is($actor)) {
            return;
        }

        $this->sendToUser($recipient, [
            'title' => $task->title,
            'body' => 'Úloha upravená · '.$this->firstName($actor),
            'icon' => url('/icons/shoplink-icon-192.svg'),
            'badge' => url('/icons/shoplink-badge.svg'),
            'tag' => 'task-update-'.$task->id,
            'data' => [
                'url' => route('tasks.edit', $task),
                'task_id' => $task->id,
                'created_by' => $actor->name,
            ],
        ]);
    }

    public function sendNewTaskComment(Task $task, User $recipient, User $author): void
    {
        if (! $recipient->web_push_notif_new_task || $recipient->is($author)) {
            return;
        }

        $this->sendToUser($recipient, [
            'title' => $task->title,
            'body' => 'Nový komentár · '.$this->firstName($author),
            'icon' => url('/icons/shoplink-icon-192.svg'),
            'badge' => url('/icons/shoplink-badge.svg'),
            'tag' => 'task-comment-'.$task->id,
            'data' => [
                'url' => route('tasks.edit', $task),
                'task_id' => $task->id,
                'created_by' => $author->name,
            ],
        ]);
    }

    public function sendToUser(User $user, array $payload): void
    {
        $subscriptions = $user->pushSubscriptions()->get();

        if ($subscriptions->isEmpty()) {
            return;
        }

        $webPush = $this->makeClient();

        if (! $webPush) {
            return;
        }

        foreach ($subscriptions as $subscription) {
            $webPush->queueNotification(
                Subscription::create([
                    'endpoint' => $subscription->endpoint,
                    'publicKey' => $subscription->public_key,
                    'authToken' => $subscription->auth_token,
                    'contentEncoding' => $subscription->content_encoding ?: 'aes128gcm',
                ]),
                json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            );
        }

        foreach ($webPush->flush() as $report) {
            $endpoint = $report->getRequest()->getUri()->__toString();

            if (! $report->isSuccess()) {
                PushSubscription::query()
                    ->where('endpoint_hash', hash('sha256', $endpoint))
                    ->delete();
            }
        }
    }

    private function makeClient(): ?WebPush
    {
        $publicKey = (string) config('services.web_push.public_key');
        $privateKey = (string) config('services.web_push.private_key');
        $subject = (string) config('services.web_push.subject');

        if ($publicKey === '' || $privateKey === '' || $subject === '') {
            return null;
        }

        return new WebPush([
            'VAPID' => [
                'subject' => $subject,
                'publicKey' => $publicKey,
                'privateKey' => $privateKey,
            ],
        ]);
    }

    private function firstName(User $user): string
    {
        return trim((string) str($user->name)->before(' ')) ?: $user->name;
    }
}
