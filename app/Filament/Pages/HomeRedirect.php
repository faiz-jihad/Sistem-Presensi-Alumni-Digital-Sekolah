<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class HomeRedirect extends BaseDashboard
{
    public function mount(): void
    {
        redirect('/admin/profile');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }
}
