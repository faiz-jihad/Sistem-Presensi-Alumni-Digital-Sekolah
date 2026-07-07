<?php

namespace App\Filament\Widgets;

use App\Models\StudentAttendance;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DailyAttendanceChartWidget extends ChartWidget
{
    protected ?string $heading = 'Status Presensi Hari Ini';

    protected ?string $description = 'Distribusi status presensi siswa berdasarkan data hari ini.';

    protected ?string $maxHeight = '300px';

    protected static ?int $sort = 6;

    public static function canView(): bool
    {
        return in_array(auth()->user()->role, ['super_admin', 'admin', 'teacher'])
            && auth()->user()->hasFeature('has_presensi');
    }

    protected int | string | array $columnSpan = [
        'default' => 1,
        'md' => 6,
        'xl' => 4,
    ];

    protected function getData(): array
    {
        $today = Carbon::today()->toDateString();

        $counts = StudentAttendance::where('date', $today)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $statuses = [
            'present' => 'Hadir',
            'late' => 'Terlambat',
            'sick' => 'Sakit',
            'permission' => 'Izin',
            'absent' => 'Alpha',
        ];

        $labels = [];
        $data = [];

        foreach ($statuses as $key => $label) {
            $labels[] = $label;
            $data[] = $counts[$key] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Status Kehadiran',
                    'data' => $data,
                    'borderWidth' => 0,
                    'backgroundColor' => [
                        'rgba(16, 185, 129, 0.85)', // Hadir (Emerald)
                        'rgba(245, 158, 11, 0.85)', // Terlambat (Amber)
                        'rgba(139, 92, 246, 0.85)', // Sakit (Violet)
                        'rgba(59, 130, 246, 0.85)', // Izin (Blue)
                        'rgba(239, 68, 68, 0.85)',  // Alpha (Rose)
                    ],
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                ],
            ],
            'cutout' => '62%',
            'maintainAspectRatio' => false,
        ];
    }
}
