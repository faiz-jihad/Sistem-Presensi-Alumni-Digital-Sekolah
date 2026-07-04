<?php

namespace App\Filament\Widgets;

use App\Models\School;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\StudentClass;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
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
        ];
    }
}
