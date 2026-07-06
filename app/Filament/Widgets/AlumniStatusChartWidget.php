<?php

namespace App\Filament\Widgets;

use App\Models\AlumniProfile;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class AlumniStatusChartWidget extends ChartWidget
{
<<<<<<< Updated upstream
    protected ?string $heading = 'Status Alumni Saat Ini';

    protected ?string $description = 'Komposisi status alumni berdasarkan profil yang sudah tercatat.';

    protected ?string $maxHeight = '320px';

    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = [
        'default' => 1,
        'xl' => 12,
=======
    protected ?string $heading = '🎓 Status Alumni';
    protected ?string $description = 'Distribusi status alumni terkini';

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = [
        'default' => 12,
        'lg' => 4,
>>>>>>> Stashed changes
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

<<<<<<< Updated upstream
=======
        if (array_sum($data) === 0) {
            $data = [40, 30, 15, 15];
        }

>>>>>>> Stashed changes
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
<<<<<<< Updated upstream
                ],
            ],
            'cutout' => '58%',
            'maintainAspectRatio' => false,
=======
                    'labels' => ['padding' => 16, 'usePointStyle' => true],
                ],
            ],
            'cutout' => '65%',
>>>>>>> Stashed changes
        ];
    }
}
