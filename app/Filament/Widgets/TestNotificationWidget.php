<?php

namespace App\Filament\Widgets;

use Filament\Notifications\Notification;
use Filament\Widgets\Widget;

class TestNotificationWidget extends Widget
{
    protected string $view = 'filament.widgets.test-notification-widget';

    protected int | string | array $columnSpan = 'full';

    protected static ?int $navigationSort = 1;

    public static function canView(): bool
    {
        return true;
    }

    public function getPushSubscriptionCount(): int
    {
        return auth()->user()?->pushSubscriptions()->count() ?? 0;
    }

    public function sendTestNotification(): void
    {
        $user = auth()->user();

        if (! $user) {
            return;
        }

        Notification::make()
            ->title('Uji Coba Notifikasi SIMPAD')
            ->body('Notifikasi database dan Web Push berhasil diproses oleh sistem.')
            ->success()
            ->sendToDatabase($user);

        Notification::make()
            ->title('Notifikasi dikirim')
            ->body('Cek bell Filament dan notifikasi browser Anda.')
            ->success()
            ->send();
    }
}