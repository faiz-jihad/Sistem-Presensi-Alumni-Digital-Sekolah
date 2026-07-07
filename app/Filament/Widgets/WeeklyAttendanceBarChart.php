<?php

namespace App\Filament\Widgets;

use App\Models\StudentAttendance;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class WeeklyAttendanceBarChart extends ChartWidget
{
    protected ?string $heading = 'Presensi Harian — 7 Hari Terakhir';
    protected ?string $description = 'Jumlah siswa hadir setiap hari';

    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = [
        'default' => 12,
        'lg' => 8,
    ];

    protected function getData(): array
    {
        $days = collect(range(6, 0))->map(fn($i) => now()->subDays($i)->toDateString());

        $labels = [];
        $hadirData = [];
        $tidakHadirData = [];

        foreach ($days as $date) {
            $labels[] = Carbon::parse($date)->locale('id')->isoFormat('ddd, D MMM');

            $hadir = StudentAttendance::where('date', $date)
                ->whereIn('status', ['present', 'late'])->count();
            $tidakHadir = StudentAttendance::where('date', $date)
                ->whereIn('status', ['absent', 'permission', 'sick'])->count();

            $hadirData[] = $hadir;
            $tidakHadirData[] = $tidakHadir;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Hadir',
                    'data' => $hadirData,
                    'backgroundColor' => 'rgba(16, 185, 129, 0.85)',
                    'borderColor' => 'rgba(16, 185, 129, 1)',
                    'borderRadius' => 6,
                    'borderWidth' => 0,
                ],
                [
                    'label' => 'Tidak Hadir',
                    'data' => $tidakHadirData,
                    'backgroundColor' => 'rgba(239, 68, 68, 0.75)',
                    'borderColor' => 'rgba(239, 68, 68, 1)',
                    'borderRadius' => 6,
                    'borderWidth' => 0,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => ['position' => 'top'],
            ],
            'scales' => [
                'y' => ['beginAtZero' => true, 'grid' => ['color' => 'rgba(156,163,175,0.15)']],
                'x' => ['grid' => ['display' => false]],
            ],
            'borderRadius' => 6,
        ];
    }
}
