<?php

namespace App\Filament\Widgets;

use App\Models\StudentAttendance;
use App\Models\StudentClass;
use App\Models\Teacher;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;
use Illuminate\Support\HtmlString;

class AttendanceChartWidget extends ChartWidget
{
    public ?string $classId = '';
    public ?string $filter = '7_days';

    protected static ?int $sort = 5;

    public static function canView(): bool
    {
        return in_array(auth()->user()->role, ['super_admin', 'admin', 'teacher'])
            && auth()->user()->hasFeature('has_presensi');
    }

    protected int | string | array $columnSpan = [
        'default' => 12,
        'lg' => 8,
    ];

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        $user = auth()->user();
        $classesQuery = StudentClass::orderBy('name');
        if ($user->role !== 'super_admin' && $user->school_id) {
            $classesQuery->where('school_id', $user->school_id);
        }
        if ($user->role === 'teacher') {
            $teacher = Teacher::where('user_id', $user->id)->first();
            if ($teacher) {
                $classesQuery->whereHas('schedules', fn($q) => $q->where('teacher_id', $teacher->id));
            }
        }
        $classes = $classesQuery->pluck('name', 'id');

        $options = '<option value="">Semua Kelas</option>';
        foreach ($classes as $id => $name) {
            $selected = $this->classId == $id ? 'selected' : '';
            $options .= "<option value=\"{$id}\" {$selected}>{$name}</option>";
        }

        $html = '
        <div class="flex items-center justify-between w-full gap-4 flex-wrap">
            <span class="text-base font-semibold leading-6 text-gray-950 dark:text-white">Tren Kehadiran Siswa</span>
            <div class="flex items-center gap-2">
                <select wire:model.live="classId" class="text-xs border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500" style="padding: 4px 12px; height: 32px;">
                    ' . $options . '
                </select>
            </div>
        </div>';

        return new HtmlString($html);
    }

    protected function getFilters(): ?array
    {
        return [
            '7_days' => '7 Hari Terakhir',
            '1_month' => '1 Bulan Terakhir',
            '1_semester' => '1 Semester (24 Minggu)',
        ];
    }

    protected function getData(): array
    {
        $timeframe = $this->filter ?? '7_days';
        $classId = $this->classId;
        $schoolId = auth()->user()->role !== 'super_admin' ? auth()->user()->school_id : null;

        $labels = [];
        $percentages = [];

        if ($timeframe === '7_days') {
            $days = collect(range(6, 0))->map(fn($i) => now()->subDays($i)->toDateString());
            foreach ($days as $date) {
                $labels[] = Carbon::parse($date)->locale('id')->isoFormat('D MMM');
                $baseQuery = StudentAttendance::where('date', $date);
                if ($schoolId) {
                    $baseQuery->where('school_id', $schoolId);
                }
                if ($classId) {
                    $baseQuery->whereHas('student', fn($q) => $q->where('class_id', $classId));
                }

                $total = (clone $baseQuery)->count();
                if ($total > 0) {
                    $present = (clone $baseQuery)->whereIn('status', ['present', 'late'])->count();
                    $percentages[] = round(($present / $total) * 100, 1);
                } else {
                    $percentages[] = null;
                }
            }
        } elseif ($timeframe === '1_month') {
            $days = collect(range(29, 0))->map(fn($i) => now()->subDays($i)->toDateString());
            foreach ($days as $date) {
                $labels[] = Carbon::parse($date)->locale('id')->isoFormat('D MMM');
                $baseQuery = StudentAttendance::where('date', $date);
                if ($schoolId) {
                    $baseQuery->where('school_id', $schoolId);
                }
                if ($classId) {
                    $baseQuery->whereHas('student', fn($q) => $q->where('class_id', $classId));
                }

                $total = (clone $baseQuery)->count();
                if ($total > 0) {
                    $present = (clone $baseQuery)->whereIn('status', ['present', 'late'])->count();
                    $percentages[] = round(($present / $total) * 100, 1);
                } else {
                    $percentages[] = null;
                }
            }
        } elseif ($timeframe === '1_semester') {
            // 24 weeks
            $weeks = collect(range(23, 0))->map(function($i) {
                $start = now()->subWeeks($i)->startOfWeek();
                $end = now()->subWeeks($i)->endOfWeek();
                return [
                    'label' => $start->locale('id')->isoFormat('D MMM') . ' - ' . $end->locale('id')->isoFormat('D MMM'),
                    'start' => $start->toDateString(),
                    'end' => $end->toDateString(),
                ];
            });

            foreach ($weeks as $week) {
                $labels[] = $week['label'];
                $baseQuery = StudentAttendance::whereBetween('date', [$week['start'], $week['end']]);
                if ($schoolId) {
                    $baseQuery->where('school_id', $schoolId);
                }
                if ($classId) {
                    $baseQuery->whereHas('student', fn($q) => $q->where('class_id', $classId));
                }

                $total = (clone $baseQuery)->count();
                if ($total > 0) {
                    $present = (clone $baseQuery)->whereIn('status', ['present', 'late'])->count();
                    $percentages[] = round(($present / $total) * 100, 1);
                } else {
                    $percentages[] = null;
                }
            }
        }

        return [
            'datasets' => [
                [
                    'label' => 'Tingkat Kehadiran (%)',
                    'data' => $percentages,
                    'backgroundColor' => 'rgba(16, 185, 129, 0.08)',
                    'borderColor' => '#10b981',
                    'borderWidth' => 3,
                    'fill' => true,
                    'tension' => 0.35,
                    'pointBackgroundColor' => '#ffffff',
                    'pointBorderColor' => '#10b981',
                    'pointBorderWidth' => 2,
                    'pointRadius' => $timeframe === '1_month' ? 1.5 : 4,
                    'pointHoverRadius' => 7,
                    'pointHoverBackgroundColor' => '#10b981',
                    'pointHoverBorderColor' => '#ffffff',
                    'pointHoverBorderWidth' => 2,
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
                'tooltip' => [
                    'backgroundColor' => '#0f172a',
                    'titleColor' => '#ffffff',
                    'bodyColor' => '#e2e8f0',
                    'padding' => 12,
                    'borderRadius' => 10,
                    'displayColors' => false,
                    'titleFont' => [
                        'weight' => 'bold',
                        'size' => 12,
                    ],
                    'bodyFont' => [
                        'size' => 11,
                    ],
                ],
            ],
            'scales' => [
                'y' => [
                    'min' => 0,
                    'max' => 100,
                    'grid' => [
                        'color' => 'rgba(148, 163, 184, 0.06)',
                        'drawBorder' => false,
                    ],
                    'ticks' => [
                        'stepSize' => 20,
                        'font' => [
                            'size' => 10,
                            'weight' => '500',
                        ],
                    ],
                ],
                'x' => [
                    'grid' => [
                        'display' => false,
                    ],
                    'ticks' => [
                        'font' => [
                            'size' => 10,
                            'weight' => '500',
                        ],
                    ],
                ],
            ],
            'interaction' => [
                'mode' => 'index',
                'intersect' => false,
            ],
        ];
    }
}
