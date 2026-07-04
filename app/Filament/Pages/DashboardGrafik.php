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

    protected ?string $heading = 'Dashboard Grafik';

    protected ?string $subheading = 'Pantau ringkasan sekolah, presensi siswa, dan status alumni dalam satu tampilan visual.';

    protected static \UnitEnum|string|null $navigationGroup = 'Laporan & Monitoring';

    protected static ?int $navigationSort = 2;

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
}
