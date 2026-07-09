<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class FilamentWebPushNotification extends Notification
{
    public function __construct(
        private readonly string $title,
        private readonly string $body,
        private readonly array $data = [],
    ) {}

    public function via(object $notifiable): array
    {
        return [WebPushChannel::class];
    }

    public function toWebPush(object $notifiable, mixed $notification): WebPushMessage
    {
        return (new WebPushMessage)
            ->title($this->title)
            ->body($this->body)
            ->icon('/favicon.ico')
            ->badge('/favicon.ico')
            ->action('Buka SIMPAD', 'open_simpad')
            ->data([
                ...$this->data,
                'url' => url($this->data['url'] ?? '/admin'),
            ]);
    }
}