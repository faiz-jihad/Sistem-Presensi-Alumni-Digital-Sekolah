<?php

namespace App\Filament\Widgets;

use App\Models\Alumni;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AlumniStatsWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $totalAlumni = Alumni::count();
        $verifiedAlumni = Alumni::verified()->count();
        $pendingAlumni = Alumni::pending()->count();
        $rejectedAlumni = Alumni::rejected()->count();

        return [
            Stat::make('Total Alumni', $totalAlumni)
                ->description('Semua alumni terdaftar')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            Stat::make('Alumni Terverifikasi', $verifiedAlumni)
                ->description('Data alumni terverifikasi')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Menunggu Verifikasi', $pendingAlumni)
                ->description('Perlu verifikasi admin')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Alumni Ditolak', $rejectedAlumni)
                ->description('Data alumni ditolak')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),
        ];
    }
}
