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
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBarSquare;

    protected static ?string $navigationLabel = 'Dashboard Grafik';

    protected static ?string $title = 'Dashboard Grafik';

    protected static \UnitEnum|string|null $navigationGroup = 'Presensi';

    protected static ?int $navigationSort = 6;

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
        return 12;
    }
}
