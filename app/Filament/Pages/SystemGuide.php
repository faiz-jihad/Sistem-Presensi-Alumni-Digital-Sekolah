<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class SystemGuide extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected string $view = 'filament.pages.system-guide';

    protected static ?string $navigationLabel = 'Panduan & Bantuan';

    protected static ?string $title = 'Panduan & Bantuan Pengguna';

    protected static \UnitEnum|string|null $navigationGroup = 'Pengaturan Sistem';

    protected static ?int $navigationSort = 110;

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }

    public static function canAccess(): bool
    {
        return in_array(auth()->user()->role, ['super_admin', 'admin', 'teacher'], true);
    }

    public function getViewData(): array
    {
        return [
            'userRole' => auth()->user()->role,
        ];
    }
}
