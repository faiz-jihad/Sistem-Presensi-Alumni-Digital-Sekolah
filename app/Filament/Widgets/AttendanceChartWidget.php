<?php

namespace App\Filament\Widgets;

use App\Models\StudentAttendance;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class AttendanceChartWidget extends ChartWidget
{
    protected ?string $heading = 'Tren Kehadiran (30 Hari Terakhir)';
    protected ?string $description = 'Persentase siswa hadir per hari';

    protected static ?int $sort = 5;

    public static function canView(): bool
    {
        return in_array(auth()->user()->role, ['super_admin', 'admin', 'teacher']);
    }

    protected int | string | array $columnSpan = [
        'default' => 12,
        'lg' => 8,
    ];

    protected function getData(): array
    {
        $days = collect(range(29, 0))->map(fn($i) => now()->subDays($i)->toDateString());

        $labels = [];
        $percentages = [];

        foreach ($days as $date) {
            $labels[] = Carbon::parse($date)->locale('id')->isoFormat('D MMM');

            $total = StudentAttendance::where('date', $date)->count();
            if ($total > 0) {
                $present = StudentAttendance::where('date', $date)
                    ->whereIn('status', ['present', 'late'])->count();
                $percentages[] = round(($present / $total) * 100, 1);
            } else {
                $percentages[] = null;
            }
        }

        return [
            'datasets' => [
                [
                    'label' => 'Tingkat Kehadiran (%)',
                    'data' => $percentages,
                    'backgroundColor' => 'rgba(99, 102, 241, 0.12)',
                    'borderColor' => 'rgb(99, 102, 241)',
                    'borderWidth' => 2.5,
                    'fill' => true,
                    'tension' => 0.4,
                    'pointBackgroundColor' => 'rgb(99, 102, 241)',
                    'pointRadius' => 3,
                    'pointHoverRadius' => 6,
                    'spanGaps' => true,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => ['display' => false],
            ],
            'scales' => [
                'y' => [
                    'min' => 0,
                    'max' => 100,
                ],
            ],
        ];
    }
}
