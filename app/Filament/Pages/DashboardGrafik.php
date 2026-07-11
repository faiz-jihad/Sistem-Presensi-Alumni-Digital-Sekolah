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
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationLabel = 'Dasbor Grafik';

    protected static ?string $title = 'Dasbor Grafik';

    protected ?string $heading = 'Dasbor Grafik';

    protected ?string $subheading = 'Pantau ringkasan sekolah, presensi siswa, dan status alumni dalam satu tampilan visual.';

    protected static ?int $navigationSort = -2;

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public function mount(): void
    {
        redirect('/admin');
    }

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
