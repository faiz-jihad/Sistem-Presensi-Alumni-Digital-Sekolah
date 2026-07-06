<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\AlumniStatsWidget;
use App\Filament\Widgets\AttendanceChartWidget;
use App\Filament\Widgets\AlumniStatusChartWidget;
use App\Filament\Widgets\WeeklyAttendanceBarChart;
use App\Filament\Widgets\RealtimeAttendanceOverviewWidget;
use App\Filament\Widgets\DashboardHeroWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationLabel = 'Beranda';
    protected static ?string $title = 'Beranda';
    protected static ?int $navigationSort = -2;

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-home';
    }

    public function getWidgets(): array
    {
        return [
<<<<<<< Updated upstream
            StatsOverview::class,
            AlumniStatsWidget::class,
            AttendanceChartWidget::class,
            AlumniStatusChartWidget::class,
            RecentSchools::class,
            RecentStudents::class,
            RecentTeachers::class,
=======
            DashboardHeroWidget::class,         // sort -1 – Hero banner + quick actions
            StatsOverview::class,               // sort  1 – Stat cards w/ sparklines
            RealtimeAttendanceOverviewWidget::class, // sort 2 – Live KBM cards
            AttendanceChartWidget::class,       // sort  3 – 30-day line chart
            AlumniStatusChartWidget::class,     // sort  3 – Doughnut alumni
            WeeklyAttendanceBarChart::class,    // sort  4 – 7-day bar chart
>>>>>>> Stashed changes
        ];
    }

    public function getColumns(): int | array
    {
        return 12;
    }
}
