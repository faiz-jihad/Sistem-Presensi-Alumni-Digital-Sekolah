<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class AdminActionsWidget extends Widget
{
    protected string $view = 'filament.widgets.admin-actions-widget';

    protected static ?int $sort = 0; // Renders on top of the dashboard!

    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        // Only show to admins/super_admins
        return auth()->user()?->hasRole(['admin', 'super_admin']) ?? false;
    }
}
