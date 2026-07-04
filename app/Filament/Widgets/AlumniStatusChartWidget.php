<?php

namespace App\Filament\Widgets;

use App\Models\AlumniProfile;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class AlumniStatusChartWidget extends ChartWidget
{
    protected ?string $heading = 'Status Alumni Saat Ini';

    protected ?string $description = 'Komposisi status alumni berdasarkan profil yang sudah tercatat.';

    protected ?string $maxHeight = '320px';

    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = [
        'default' => 1,
        'xl' => 12,
    ];

    protected function getData(): array
    {
        $statusCounts = AlumniProfile::select('current_status', DB::raw('count(*) as total'))
            ->groupBy('current_status')
            ->pluck('total', 'current_status')
            ->toArray();

        $statuses = [
            'working' => 'Bekerja',
            'studying' => 'Kuliah',
            'entrepreneur' => 'Wirausaha',
            'studying_working' => 'Kuliah & Kerja',
            'unemployed' => 'Mencari Kerja',
        ];

        $labels = [];
        $data = [];

        foreach ($statuses as $key => $label) {
            $labels[] = $label;
            // Jika ada di database, gunakan nilainya; jika tidak, default 0
            $data[] = $statusCounts[$key] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Alumni',
                    'data' => $data,
                    'borderWidth' => 0,
                    'backgroundColor' => [
                        'rgba(16, 185, 129, 0.8)', // Bekerja (Emerald)
                        'rgba(59, 130, 246, 0.8)', // Kuliah (Blue)
                        'rgba(245, 158, 11, 0.8)', // Wirausaha (Amber)
                        'rgba(139, 92, 246, 0.8)', // Kuliah & Kerja (Violet)
                        'rgba(239, 68, 68, 0.8)',  // Mencari Kerja (Rose)
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
            'cutout' => '58%',
            'maintainAspectRatio' => false,
        ];
    }
}
