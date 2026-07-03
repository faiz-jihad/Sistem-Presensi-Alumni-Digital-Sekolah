<?php

namespace App\Filament\Widgets;

use App\Models\School;
use App\Models\Student;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalUsers    = User::count();
        $totalSchools  = School::count();
        $totalStudents = Student::count();
        $activeUsers   = User::where('status', 'active')->count();

        return [
            Stat::make('Total Sekolah', $totalSchools)
                ->description('Sekolah terdaftar')
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('success'),

            Stat::make('Total Pengguna', $totalUsers)
                ->description("{$activeUsers} pengguna aktif")
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),

            Stat::make('Total Siswa', $totalStudents)
                ->description('Siswa terdaftar')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('warning'),

            Stat::make('Admin', User::whereIn('role', ['super_admin', 'admin'])->count())
                ->description('Super Admin & Admin')
                ->descriptionIcon('heroicon-m-shield-check')
                ->color('danger'),
        ];
    }
}
