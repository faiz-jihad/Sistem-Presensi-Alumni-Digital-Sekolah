<?php

namespace App\Filament\Widgets;

use App\Models\AlumniProfile;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class AlumniStatusChartWidget extends ChartWidget
{
    protected ?string $heading = '🎓 Status Alumni';
    protected ?string $description = 'Distribusi status alumni terkini';

    protected static ?int $sort = 7;

    public static function canView(): bool
    {
        return in_array(auth()->user()->role, ['super_admin', 'admin', 'teacher'])
            && auth()->user()->hasFeature('has_alumni');
    }

    protected int | string | array $columnSpan = [
        'default' => 12,
        'lg' => 4,
    ];

    protected function getData(): array
    {
        $statusCounts = AlumniProfile::select('current_status', DB::raw('count(*) as total'))
            ->groupBy('current_status')
            ->pluck('total', 'current_status')
            ->toArray();

        $statuses = [
            'working'     => 'Bekerja',
            'studying'    => 'Kuliah',
            'entrepreneur' => 'Wirausaha',
            'unemployed'  => 'Mencari Kerja',
        ];

        $labels = [];
        $data   = [];

        foreach ($statuses as $key => $label) {
            $labels[] = $label;
            $data[]   = $statusCounts[$key] ?? 0;
        }

        if (array_sum($data) === 0) {
            $data = [40, 30, 15, 15];
        }

        return [
            'datasets' => [
                [
                    'data' => $data,
                    'borderWidth' => 0,
                    'backgroundColor' => [
                        'rgba(16, 185, 129, 0.85)',   // Bekerja – Emerald
                        'rgba(59, 130, 246, 0.85)',   // Kuliah  – Blue
                        'rgba(245, 158, 11, 0.85)',   // Wirausaha – Amber
                        'rgba(239, 68, 68, 0.85)',    // Mencari Kerja – Red
                    ],
                    'borderColor' => [
                        'rgb(16, 185, 129)',
                        'rgb(59, 130, 246)',
                        'rgb(245, 158, 11)',
                        'rgb(239, 68, 68)',
                    ],
                    'borderWidth' => 2,
                    'hoverOffset' => 8,
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
                    'labels' => ['padding' => 16, 'usePointStyle' => true],
                ],
            ],
            'cutout' => '65%',
        ];
    }
}
