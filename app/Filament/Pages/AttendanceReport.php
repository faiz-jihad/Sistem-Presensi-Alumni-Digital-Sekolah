<?php

namespace App\Filament\Pages;

use App\Models\StudentClass;
use App\Services\ReportService;
use App\Exports\DailyAttendanceExport;
use App\Exports\MonthlyAttendanceExport;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Grid;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Notifications\Notification;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceReport extends Page
{
    protected string $view = 'filament.pages.attendance-report';

    protected static ?string $navigationLabel = 'Rekap Bulanan';

    protected static string|\UnitEnum|null $navigationGroup = 'Laporan & Monitoring';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentChartBar;

    protected static ?int $navigationSort = 2;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'type' => 'daily',
            'class_id' => null,
            'date' => now()->toDateString(),
            'month' => now()->month,
            'year' => now()->year,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Grid::make([
                    'default' => 1,
                    'md' => 2,
                    'xl' => 5,
                ])
                    ->schema([
                        Select::make('type')
                            ->label('Jenis Laporan')
                            ->options([
                                'daily' => 'Harian',
                                'monthly' => 'Bulanan',
                            ])
                            ->native(false)
                            ->live()
                            ->required(),

                        Select::make('class_id')
                            ->label('Kelas')
                            ->options(StudentClass::orderBy('name')->pluck('name', 'id'))
                            ->placeholder('Pilih Kelas')
                            ->searchable()
                            ->native(false)
                            ->live()
                            ->required(),

                        DatePicker::make('date')
                            ->label('Tanggal')
                            ->required()
                            ->live()
                            ->visible(fn(callable $get) => $get('type') === 'daily'),

                        Select::make('month')
                            ->label('Bulan')
                            ->options([
                                1 => 'Januari',
                                2 => 'Februari',
                                3 => 'Maret',
                                4 => 'April',
                                5 => 'Mei',
                                6 => 'Juni',
                                7 => 'Juli',
                                8 => 'Agustus',
                                9 => 'September',
                                10 => 'Oktober',
                                11 => 'November',
                                12 => 'Desember'
                            ])
                            ->native(false)
                            ->required()
                            ->live()
                            ->visible(fn(callable $get) => $get('type') === 'monthly'),

                        Select::make('year')
                            ->label('Tahun')
                            ->options(array_combine(range(2020, 2030), range(2020, 2030)))
                            ->native(false)
                            ->required()
                            ->live()
                            ->visible(fn(callable $get) => $get('type') === 'monthly'),
                    ])
            ])
            ->statePath('data');
    }

    public function getReport(): ?array
    {
        $classId = $this->data['class_id'] ?? null;
        if (! $classId) {
            return null;
        }

        $service = app(ReportService::class);
        $schoolId = DB::table('classes')->where('id', $classId)->value('school_id');

        if (! $schoolId) {
            return null;
        }

        try {
            if ($this->data['type'] === 'daily') {
                $date = $this->data['date'] ?? now()->toDateString();
                return $service->getDailyReport($date, $classId, $schoolId);
            } else {
                $month = (int) ($this->data['month'] ?? now()->month);
                $year = (int) ($this->data['year'] ?? now()->year);
                return $service->getMonthlyReport($month, $year, $classId, $schoolId);
            }
        } catch (\Exception $e) {
            return null;
        }
    }

    public function exportExcel()
    {
        $report = $this->getReport();
        if (! $report) {
            Notification::make()->title('Data tidak tersedia untuk diekspor.')->danger()->send();
            return null;
        }

        $className = $report['class']['name'] ?? 'Kelas';

        if ($this->data['type'] === 'daily') {
            $date = $report['date'];
            $filename = "rekap_harian_{$className}_{$date}.xlsx";
            return Excel::download(
                new DailyAttendanceExport($report['students'], "Harian {$className}"),
                $filename
            );
        } else {
            $monthName = Carbon::createFromDate($this->data['year'], $this->data['month'], 1)->format('M');
            $filename = "rekap_bulanan_{$className}_{$monthName}_{$this->data['year']}.xlsx";
            return Excel::download(
                new MonthlyAttendanceExport($report['students'], "Bulanan {$className}"),
                $filename
            );
        }
    }

    public function exportPdf()
    {
        $report = $this->getReport();
        if (! $report) {
            Notification::make()->title('Data tidak tersedia untuk diekspor.')->danger()->send();
            return null;
        }

        $className = $report['class']['name'] ?? 'Kelas';

        if ($this->data['type'] === 'daily') {
            $date = $report['date'];
            $pdf = Pdf::loadView('pdf.daily-attendance', $report);
            return response()->streamDownload(
                fn() => print($pdf->output()),
                "rekap_harian_{$className}_{$date}.pdf"
            );
        } else {
            $month = $this->data['month'];
            $year = $this->data['year'];
            $pdf = Pdf::loadView('pdf.monthly-attendance', $report);
            return response()->streamDownload(
                fn() => print($pdf->output()),
                "rekap_bulanan_{$className}_{$month}_{$year}.pdf"
            );
        }
    }
}
