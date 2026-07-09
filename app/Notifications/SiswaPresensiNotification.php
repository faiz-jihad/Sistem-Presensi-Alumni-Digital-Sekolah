<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class SiswaPresensiNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected string $title;
    protected string $body;
    protected array $dataPayload;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $title, string $body, array $dataPayload = [])
    {
        $this->title = $title;
        $this->body = $body;
        $this->dataPayload = $dataPayload;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', WebPushChannel::class];
    }

    /**
     * Get the array representation of the notification for database storage.
     * Special format to match Filament's notification bell structure.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return \Filament\Notifications\Notification::make()
            ->title($this->title)
            ->body($this->body)
            ->success()
            ->getDatabaseMessage();
    }

    /**
     * Get the web push representation of the notification.
     */
    public function toWebPush(object $notifiable, $notification): WebPushMessage
    {
        return (new WebPushMessage)
            ->title($this->title)
            ->icon('/favicon.ico')
            ->body($this->body)
            ->action('Buka SIMPAD', 'open_simpad')
            ->data([
                'url' => url('/admin'),
                ...$this->dataPayload
            ]);
    }
}
