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
    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
<<<<<<< Updated upstream
        $schoolCount = School::count();
        $activeTeacherCount = Teacher::where('status', 'active')->count();
        $activeStudentCount = Student::where('status', 'active')->count();
        $activeClassCount = StudentClass::where('status', 'active')->count();

        return [
            Stat::make('Total Sekolah', $schoolCount)
                ->description('Sekolah terdaftar di sistem')
                ->descriptionIcon('heroicon-m-building-office')
                ->chart(array_fill(0, 7, $schoolCount))
                ->color('primary'),

            Stat::make('Guru Aktif', $activeTeacherCount)
                ->description('Guru dengan status aktif')
                ->descriptionIcon('heroicon-m-user-group')
                ->chart(array_fill(0, 7, $activeTeacherCount))
                ->color('success'),

            Stat::make('Siswa Aktif', $activeStudentCount)
                ->description('Siswa dengan status aktif')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->chart(array_fill(0, 7, $activeStudentCount))
                ->color('warning'),

            Stat::make('Kelas Aktif', $activeClassCount)
                ->description('Kelas yang sedang berjalan')
                ->descriptionIcon('heroicon-m-rectangle-stack')
                ->chart(array_fill(0, 7, $activeClassCount))
                ->color('danger'),
=======
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
                ->descriptionIcon($presentDiff >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($presentDiff >= 0 ? 'success' : 'warning')
                ->chart($hadirTrend),

            Stat::make('Tidak Hadir / Alpha', $absentToday)
                ->description('Alpha hari ini')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger')
                ->chart($alphaTrend),

            Stat::make('Izin / Sakit', $permissionToday)
                ->description('Mengajukan izin/sakit')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('warning'),

            Stat::make('Total Siswa Terdaftar', $totalStudents)
                ->description("{$totalTeachers} guru aktif")
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('primary'),
>>>>>>> Stashed changes
        ];
    }
}
