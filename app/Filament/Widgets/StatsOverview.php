<?php

namespace App\Filament\Widgets;

use App\Models\School;
use App\Models\Student;
use App\Models\Teacher;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Sekolah', School::count())->color('primary'),
            Stat::make('Guru', Teacher::count())->color('success'),
            Stat::make('Siswa', Student::count())->color('warning'),
        ];
    }
}
