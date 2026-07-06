<?php

namespace App\Filament\Widgets;

use App\Models\StudentAttendance;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceChartWidget extends ChartWidget
{
    protected ?string $heading = 'Tren Kehadiran 7 Hari';

    protected ?string $description = 'Persentase hadir dan terlambat dibanding total presensi yang tercatat.';

    protected ?string $maxHeight = '300px';

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = [
        'default' => 1,
        'md' => 6,
        'xl' => 8,
    ];

    protected function getData(): array
    {
        $dates = collect(range(6, 0))->map(function ($i) {
            return now()->subDays($i)->toDateString();
        });

        $labels = [];
        $percentages = [];

        foreach ($dates as $date) {
            $formattedDate = Carbon::parse($date)->locale('id')->isoFormat('dddd');
            $labels[] = $formattedDate;

            // Query data kehadiran riil
            $total = StudentAttendance::where('date', $date)->count();
            if ($total > 0) {
                $present = StudentAttendance::where('date', $date)
                    ->whereIn('status', ['present', 'late'])
                    ->count();
                $percentages[] = round(($present / $total) * 100, 1);
            } else {
                $percentages[] = 0;
            }
        }

        return [
            'datasets' => [
                [
                    'label' => 'Tingkat Kehadiran (%)',
                    'data' => $percentages,
                    'backgroundColor' => 'rgba(37, 99, 235, 0.1)',
                    'borderColor' => 'rgb(37, 99, 235)',
                    'borderWidth' => 3,
                    'pointBackgroundColor' => 'rgb(37, 99, 235)',
                    'pointBorderWidth' => 0,
                    'pointRadius' => 4,
                    'fill' => true,
                    'tension' => 0.4,
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
            'maintainAspectRatio' => false,
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'max' => 100,
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
        ];
    }
}
