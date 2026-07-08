<?php

namespace App\Filament\Widgets;

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
}
