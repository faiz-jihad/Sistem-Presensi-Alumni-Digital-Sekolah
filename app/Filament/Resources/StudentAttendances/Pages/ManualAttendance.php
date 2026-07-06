<?php

namespace App\Filament\Resources\StudentAttendances\Pages;

use App\Models\Student;
use App\Models\StudentClass;
use App\Models\Teacher;
use App\Services\AttendanceService;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ManualAttendance extends Page
{
    protected string $view = 'filament.resources.student-attendances.pages.manual-attendance';

    protected static ?string $navigationLabel = 'Presensi Manual Guru';

    protected static string|\UnitEnum|null $navigationGroup = 'Presensi';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedPencil;

    protected static ?int $navigationSort = 3;

    public ?array $data = [];

    public function mount(): void
    {
        $this->data = [
            'date'        => now()->toDateString(),
            'class_id'    => null,
            'attendances' => [],
        ];
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('class_id')
                    ->label('Kelas')
                    ->options(StudentClass::orderBy('name')->pluck('name', 'id'))
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn (callable $set) => $set('attendances', [])),

                DatePicker::make('date')
                    ->label('Tanggal Presensi')
                    ->required()
                    ->default(now()),

                Repeater::make('attendances')
                    ->label('Daftar Kehadiran Siswa')
                    ->schema([
                        Select::make('student_id')
                            ->label('Nama Siswa')
                            ->options(function (callable $get) {
                                $classId = $get('../../class_id');
                                if (! $classId) {
                                    return [];
                                }

                                return Student::where('class_id', $classId)
                                    ->where('status', 'active')
                                    ->orderBy('name')
                                    ->pluck('name', 'id');
                            })
                            ->required()
                            ->searchable(),

                        Select::make('status')
                            ->label('Status Kehadiran')
                            ->options([
                                'present'    => 'Hadir',
                                'late'       => 'Terlambat',
                                'sick'       => 'Sakit',
                                'permission' => 'Izin',
                                'absent'     => 'Alpha',
                            ])
                            ->default('present')
                            ->required()
                            ->live(),

                        Textarea::make('note')
                            ->label('Catatan')
                            ->rows(1)
                            ->placeholder('Opsional...')
                            ->maxLength(255),
                    ])
                    ->columns(3)
                    ->addActionLabel('+ Tambah Siswa')
                    ->reorderable(false)
                    ->collapsible()
                    ->defaultItems(0),
            ])
            ->statePath('data');
    }

    /**
     * Auto-populate repeater dengan semua siswa aktif di kelas
     */
    public function loadAllStudents(): void
    {
        $classId = $this->data['class_id'] ?? null;

        if (! $classId) {
            Notification::make()
                ->title('Pilih kelas terlebih dahulu')
                ->warning()
                ->send();
            return;
        }

        $students = Student::where('class_id', $classId)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        if ($students->isEmpty()) {
            Notification::make()
                ->title('Tidak ada siswa aktif')
                ->body('Tidak ada siswa aktif yang ditemukan di kelas ini.')
                ->warning()
                ->send();
            return;
        }

        $this->data['attendances'] = $students->map(fn ($s) => [
            'student_id' => $s->id,
            'status'     => 'present',
            'note'       => '',
        ])->toArray();

        Notification::make()
            ->title($students->count() . ' siswa berhasil dimuat')
            ->body('Ubah status kehadiran sesuai kondisi masing-masing siswa.')
            ->success()
            ->send();
    }

    public function submit(): void
    {
        $data = $this->data;

        $teacher = Teacher::where('user_id', auth()->id())->first();

        if (! $teacher) {
            Notification::make()
                ->title('Akun guru tidak ditemukan')
                ->body('Pastikan akun Anda terdaftar sebagai guru.')
                ->danger()
                ->send();
            return;
        }

        if (empty($data['attendances'])) {
            Notification::make()
                ->title('Daftar kehadiran kosong')
                ->body('Silakan tambahkan atau muat daftar siswa terlebih dahulu.')
                ->warning()
                ->send();
            return;
        }

        $service = app(AttendanceService::class);
        $result  = $service->recordClassAttendance(
            $teacher->id,
            $data['class_id'],
            $data['date'],
            $data['attendances']
        );

        Notification::make()
            ->title('Presensi berhasil disimpan')
            ->body('Total ' . $result['count'] . ' siswa berhasil direkap. Notifikasi WhatsApp dikirim ke orang tua.')
            ->success()
            ->send();

        // Reset repeater setelah submit
        $this->data['attendances'] = [];
    }

    /**
     * Hitung statistik dari data repeater untuk ditampilkan di view
     */
    public function getAttendanceSummary(): array
    {
        $counts = [
            'present'    => 0,
            'late'       => 0,
            'sick'       => 0,
            'permission' => 0,
            'absent'     => 0,
            'total'      => 0,
        ];

        foreach ($this->data['attendances'] ?? [] as $att) {
            $status = $att['status'] ?? 'present';
            if (array_key_exists($status, $counts)) {
                $counts[$status]++;
            }
            $counts['total']++;
        }

        return $counts;
    }
}
