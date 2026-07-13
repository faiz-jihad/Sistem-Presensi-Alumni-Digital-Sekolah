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
        'default' => 12,
        'lg' => 4,
    ];

    protected function getData(): array
    {
        $today = Carbon::today()->toDateString();
        $schoolId = auth()->user()->role !== 'super_admin' ? auth()->user()->school_id : null;

        $query = StudentAttendance::where('date', $today);
        if ($schoolId) {
            $query->where('school_id', $schoolId);
        }

        $counts = $query
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
                    'borderWidth' => 4,
                    'borderColor' => 'transparent',
                    'borderRadius' => 8,
                    'backgroundColor' => [
                        '#10b981', // Hadir (Emerald)
                        '#f59e0b', // Terlambat (Amber)
                        '#8b5cf6', // Sakit (Violet)
                        '#3b82f6', // Izin (Blue)
                        '#ef4444', // Alpa (Rose)
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
                    'labels' => [
                        'usePointStyle' => true,
                        'padding' => 20,
                        'font' => [
                            'size' => 11,
                            'weight' => '500',
                        ],
                    ],
                ],
            ],
            'cutout' => '75%',
            'maintainAspectRatio' => false,
        ];
    }
}
