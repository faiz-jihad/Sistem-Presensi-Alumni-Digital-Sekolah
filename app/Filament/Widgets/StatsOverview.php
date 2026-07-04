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
        return [
            Stat::make('Total Sekolah', School::count())
                ->description('Sekolah terdaftar di sistem')
                ->descriptionIcon('heroicon-m-building-office')
                ->color('primary'),

            Stat::make('Total Guru', Teacher::count())
                ->description('Guru pengajar aktif')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success'),

            Stat::make('Total Siswa', Student::count())
                ->description('Siswa terdaftar aktif')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('warning'),

            Stat::make('Total Kelas', StudentClass::count())
                ->description('Kelas aktif berjalan')
                ->descriptionIcon('heroicon-m-rectangle-stack')
                ->color('danger'),
        ];
    }
}
