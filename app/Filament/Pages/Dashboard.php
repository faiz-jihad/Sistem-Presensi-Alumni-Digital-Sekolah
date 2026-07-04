<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\AttendanceChartWidget;
use App\Filament\Widgets\AlumniStatusChartWidget;
use App\Filament\Widgets\RecentSchools;
use App\Filament\Widgets\RecentStudents;
use App\Filament\Widgets\RecentTeachers;
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
            StatsOverview::class,
            AttendanceChartWidget::class,
            AlumniStatusChartWidget::class,
            RecentSchools::class,
            RecentStudents::class,
            RecentTeachers::class,
        ];
    }

    public function getColumns(): int | array
    {
        return 12;
    }
}
