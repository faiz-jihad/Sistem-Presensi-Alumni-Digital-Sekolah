<?php

namespace App\Filament\Widgets;

use App\Models\School;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\StudentClass;
use App\Models\StudentAttendance;
use App\Models\Alumni;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 2;

    public static function canView(): bool
    {
        return in_array(auth()->user()->role, ['super_admin', 'admin', 'teacher']);
    }

    protected int | string | array $columnSpan = 'full';

    protected ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        $today = Carbon::today()->toDateString();
        $yesterday = Carbon::yesterday()->toDateString();

        // Today's attendance
        $presentToday  = StudentAttendance::where('date', $today)->whereIn('status', ['present', 'late'])->count();
        $absentToday   = StudentAttendance::where('date', $today)->whereIn('status', ['absent'])->count();
        $permissionToday = StudentAttendance::where('date', $today)->whereIn('status', ['permission', 'sick'])->count();

        // Trend sparklines (7 days)
        $trendDays = collect(range(6, 0))->map(fn($i) => now()->subDays($i)->toDateString());

        $hadirTrend = $trendDays->map(fn($d) =>
            StudentAttendance::where('date', $d)->whereIn('status', ['present', 'late'])->count()
        )->toArray();

        $alphaTrend = $trendDays->map(fn($d) =>
            StudentAttendance::where('date', $d)->where('status', 'absent')->count()
        )->toArray();

        $presentYesterday = StudentAttendance::where('date', $yesterday)->whereIn('status', ['present', 'late'])->count();
        $presentDiff = $presentToday - $presentYesterday;

        $totalStudents = Student::count();
        $totalTeachers = Teacher::count();

        return [
            Stat::make('Hadir Hari Ini', $presentToday)
                ->description($presentDiff >= 0
                    ? "↑ {$presentDiff} dari kemarin"
                    : "↓ " . abs($presentDiff) . " dari kemarin")
                ->color($presentDiff >= 0 ? 'success' : 'warning')
                ->chart($hadirTrend),

            Stat::make('Tidak Hadir / Alpha', $absentToday)
                ->description('Alpha hari ini')
                ->color('danger')
                ->chart($alphaTrend),

            Stat::make('Izin / Sakit', $permissionToday)
                ->description('Mengajukan izin/sakit')
                ->color('warning'),

            Stat::make('Total Siswa Terdaftar', $totalStudents)
                ->description("{$totalTeachers} guru aktif")
                ->color('primary'),
        ];
    }
}
