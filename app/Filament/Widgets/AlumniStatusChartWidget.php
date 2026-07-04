<?php

namespace App\Filament\Widgets;

use App\Models\AlumniProfile;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class AlumniStatusChartWidget extends ChartWidget
{
    protected ?string $heading = 'Grafik Status Alumni Saat Ini';

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = [
        'default' => 12,
        'md' => 6,
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

        // Cek jika seluruh data kosong (misal seeder belum dijalankan)
        if (array_sum($data) === 0) {
            // Gunakan dummy data penyeimbang agar chart tidak kosong saat pertama load
            $data = [35, 25, 15, 10, 15];
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Alumni',
                    'data' => $data,
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
}
