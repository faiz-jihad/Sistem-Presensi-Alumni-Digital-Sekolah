<?php

namespace App\Filament\Pages;

use App\Models\Student;
use App\Services\ReportService;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Support\Icons\Heroicon;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;

class WhatsappNotifPage extends Page implements HasForms, HasActions
{
    use InteractsWithForms;
    use InteractsWithActions;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected string $view = 'filament.pages.whatsapp-notif';

    protected static ?string $navigationLabel = 'Kirim WA Orang Tua';

    protected static \UnitEnum|string|null $navigationGroup = 'Presensi & Kehadiran';

    protected static ?int $navigationSort = 5;

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'type'  => 'daily',
            'date'  => now()->toDateString(),
            'month' => now()->month,
            'year'  => now()->year,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Kirim Notifikasi WhatsApp')
                    ->description('Pilih jenis rekap dan tanggal untuk mengirim notifikasi ke orang tua siswa')
                    ->icon(Heroicon::OutlinedChatBubbleLeftRight)
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Select::make('type')
                                    ->label('Jenis Rekap')
                                    ->prefixIcon(Heroicon::OutlinedQueueList)
                                    ->options([
                                        'daily'   => 'Harian',
                                        'monthly' => 'Bulanan',
                                    ])
                                    ->required()
                                    ->live()
                                    ->default('daily')
                                    ->native(false)
                                    ->columnSpan(1),

                                DatePicker::make('date')
                                    ->label('Pilih Tanggal')
                                    ->prefixIcon(Heroicon::OutlinedCalendarDays)
                                    ->required()
                                    ->visible(fn ($get) => $get('type') === 'daily')
                                    ->default(now())
                                    ->native(false)
                                    ->displayFormat('d M Y')
                                    ->columnSpan(1),

                                Select::make('month')
                                    ->label('Pilih Bulan')
                                    ->prefixIcon(Heroicon::OutlinedCalendar)
                                    ->options([
                                        1  => 'Januari', 2  => 'Februari', 3  => 'Maret',
                                        4  => 'April',   5  => 'Mei',      6  => 'Juni',
                                        7  => 'Juli',    8  => 'Agustus',  9  => 'September',
                                        10 => 'Oktober', 11 => 'November', 12 => 'Desember',
                                    ])
                                    ->required()
                                    ->visible(fn ($get) => $get('type') === 'monthly')
                                    ->default(now()->month)
                                    ->native(false)
                                    ->columnSpan(1),

                                Select::make('year')
                                    ->label('Pilih Tahun')
                                    ->prefixIcon(Heroicon::OutlinedCalendarDays)
                                    ->options(array_combine(range(2020, 2030), range(2020, 2030)))
                                    ->required()
                                    ->visible(fn ($get) => $get('type') === 'monthly')
                                    ->default(now()->year)
                                    ->native(false)
                                    ->columnSpan(1),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed(false)
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    /**
     * Hitung total orang tua yang akan mendapat notifikasi
     */
    public function getRecipientCount(): int
    {
        return Student::where('status', 'active')
            ->where(function ($q) {
                $q->whereNotNull('parent_phone')
                  ->where('parent_phone', '!=', '');
            })
            ->count();
    }

    /**
     * Get info statistik untuk ditampilkan di view
     */
    public function getStats(): array
    {
        $totalStudents = Student::where('status', 'active')->count();
        $hasPhone = Student::where('status', 'active')
            ->whereNotNull('parent_phone')
            ->where('parent_phone', '!=', '')
            ->count();

        return [
            'total_students' => $totalStudents,
            'has_phone' => $hasPhone,
            'no_phone' => $totalStudents - $hasPhone,
        ];
    }

    public function sendNotifAction(): Action
    {
        return Action::make('sendNotif')
            ->label('Kirim Notifikasi')
            ->icon(Heroicon::OutlinedPaperAirplane)
            ->color('success')
            ->size('lg')
            ->requiresConfirmation()
            ->modalHeading('Konfirmasi Kirim Notifikasi')
            ->modalDescription(function () {
                $count = $this->getRecipientCount();
                $type = $this->data['type'] ?? 'daily';
                
                if ($type === 'daily') {
                    $date = $this->data['date'] ?? now()->toDateString();
                    $label = Carbon::parse($date)->locale('id')->isoFormat('D MMMM Y');
                    return "Anda akan mengirim notifikasi rekap harian tanggal {$label} kepada {$count} orang tua siswa. Lanjutkan?";
                } else {
                    $month = (int) ($this->data['month'] ?? now()->month);
                    $year = (int) ($this->data['year'] ?? now()->year);
                    $label = Carbon::createFromDate($year, $month, 1)->locale('id')->isoFormat('MMMM Y');
                    return "Anda akan mengirim notifikasi rekap bulanan {$label} kepada {$count} orang tua siswa. Lanjutkan?";
                }
            })
            ->modalIcon(Heroicon::OutlinedExclamationTriangle)
            ->action(function () {
                $this->sendNotif();
            });
    }

    public function sendNotif(): void
    {
        $type  = $this->data['type'] ?? 'daily';
        $service = app(ReportService::class);

        try {
            if ($type === 'daily') {
                $date  = $this->data['date'] ?? now()->toDateString();
                $count = $service->sendDailyRecapToParents($date);
                $label = Carbon::parse($date)->locale('id')->isoFormat('D MMMM Y');
                $message = "✅ Rekap harian tanggal {$label} berhasil dikirim ke {$count} orang tua.";
            } else {
                $month = (int) ($this->data['month'] ?? now()->month);
                $year  = (int) ($this->data['year']  ?? now()->year);
                $count = $service->sendMonthlyRecapToParents($month, $year);
                $label = Carbon::createFromDate($year, $month, 1)->locale('id')->isoFormat('MMMM Y');
                $message = "✅ Rekap bulanan {$label} berhasil dikirim ke {$count} orang tua.";
            }

            Notification::make()
                ->title('Notifikasi WhatsApp Berhasil Dikirim!')
                ->body($message)
                ->success()
                ->send()
                ->sendToDatabase(auth()->user());

        } catch (\Exception $e) {
            Notification::make()
                ->title('Gagal Mengirim Notifikasi')
                ->body('Error: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            $this->sendNotifAction(),
        ];
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();

        if ($user->role === 'super_admin') {
            return true;
        }

        return $user->school?->status === 'active';
    }
}