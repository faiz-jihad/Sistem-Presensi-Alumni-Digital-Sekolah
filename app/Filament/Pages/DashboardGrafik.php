<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\AlumniStatusChartWidget;
use App\Filament\Widgets\AttendanceChartWidget;
use App\Filament\Widgets\DailyAttendanceChartWidget;
use App\Filament\Widgets\StatsOverview;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class DashboardGrafik extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-home';

    protected static ?string $navigationLabel = 'Beranda';

    protected static ?string $title = 'Beranda';

    protected ?string $heading = 'Beranda';

    protected ?string $subheading = 'Pantau ringkasan sekolah, presensi siswa, dan status alumni dalam satu tampilan visual.';

    protected static ?int $navigationSort = -2;

    protected string $view = 'filament.pages.dashboard-grafik';

    protected function getHeaderWidgets(): array
    {
        return [
            StatsOverview::class,
            DailyAttendanceChartWidget::class,
            AttendanceChartWidget::class,
            AlumniStatusChartWidget::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int | array
    {
        return [
            'default' => 1,
            'md' => 6,
            'xl' => 12,
        ];
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();

        if ($user->role === 'super_admin') {
            return true;
        }

        return $user->school?->status === 'active';
    }
}
