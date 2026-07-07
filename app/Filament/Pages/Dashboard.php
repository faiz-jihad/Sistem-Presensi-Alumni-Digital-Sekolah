<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\StatsOverview;
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

    public function getView(): string
    {
        $user = auth()->user();
        if ($user->role !== 'super_admin' && $user->school?->status === 'inactive') {
            return 'filament.pages.inactive-school';
        }
        return parent::getView();
    }

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-home';
    }

    public function getWidgets(): array
    {
        $user = auth()->user();

        // Sekolah tidak aktif
        if (
            $user->role !== 'super_admin'
            && $user->school?->status === 'inactive'
        ) {
            return [];
        }

        return [
            DashboardHeroWidget::class,
            StatsOverview::class,
            RealtimeAttendanceOverviewWidget::class,
            AttendanceChartWidget::class,
            AlumniStatusChartWidget::class,
            WeeklyAttendanceBarChart::class,
        ];
    }

    public function getColumns(): int|array
    {
        return 12;
    }
}