<?php

namespace App\Filament\Resources\StudentAttendances\Pages;

use App\Models\PresensiSession;
use App\Models\Student;
use App\Models\StudentClass;
use App\Models\Teacher;
use App\Models\StudentAttendance;
use App\Enums\AttendanceStatus;
use App\Services\AttendanceService;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ManualAttendance extends Page
{
    protected string $view = 'filament.resources.student-attendances.pages.manual-attendance';

    protected static ?string $navigationLabel = 'Input Presensi Manual';

    protected static string|\UnitEnum|null $navigationGroup = 'Presensi & Kehadiran';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedPencil;

    protected static ?int $navigationSort = 2;

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public ?array $data = [];

    public function mount(): void
    {
        $sessionId = request()->query('session_id');
        $session = $sessionId ? PresensiSession::with('schedule')->find($sessionId) : null;

        $this->data = [
            'date'                => $session ? $session->date : now()->toDateString(),
            'class_id'            => $session ? $session->schedule?->class_id : null,
            'presensi_session_id' => $session ? $session->id : null,
            'attendances'         => [],
        ];

        if ($session && $session->schedule?->class_id) {
            // Load existing attendances if they exist
            $existingAttendances = StudentAttendance::where('presensi_session_id', $session->id)->get();

            if ($existingAttendances->isNotEmpty()) {
                $this->data['attendances'] = $existingAttendances->map(fn($att) => [
                    'student_id' => $att->student_id,
                    'status'     => $att->status instanceof AttendanceStatus ? $att->status->value : $att->status,
                    'note'       => $att->note,
                ])->toArray();
            } else {
                // Otherwise, load active students in the class
                $students = Student::where('class_id', $session->schedule->class_id)
                    ->where('status', 'active')
                    ->orderBy('name')
                    ->get();

                $this->data['attendances'] = $students->map(fn($s) => [
                    'student_id' => $s->id,
                    'status'     => 'present',
                    'note'       => '',
                ])->toArray();
            }
        }
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('class_id')
                    ->label('Kelas')
                    ->options(StudentClass::orderBy('name')->pluck('name', 'id'))
                    ->required()
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(fn(callable $set) => $set('attendances', [])),

                DatePicker::make('date')
                    ->label('Tanggal')
                    ->required()
                    ->default(now()),

                Select::make('presensi_session_id')
                    ->label('Sesi Presensi')
                    ->helperText('Pilih sesi yang sesuai agar rekap presensi terkait dengan sesi jadwal yang benar.')
                    ->options(function (callable $get) {
                        $classId = $get('class_id');
                        $date = $get('date');

                        if (! $classId || ! $date) {
                            return [];
                        }

                        return PresensiSession::query()
                            ->where('date', $date)
                            ->whereHas('schedule', fn($query) => $query->where('class_id', $classId))
                            ->orderBy('start_time')
                            ->get()
                            ->mapWithKeys(function (PresensiSession $session) {
                                $label = $session->schedule?->subject?->name ?? 'Sesi';
                                $timeLabel = $session->start_time ? ' • ' . $session->start_time : '';
                                $teacherLabel = $session->teacher?->name ? ' • ' . $session->teacher->name : '';

                                return [$session->id => $label . $timeLabel . $teacherLabel];
                            })
                            ->toArray();
                    })
                    ->searchable()
                    ->preload()
                    ->required(),

                Repeater::make('attendances')
                    ->label('Daftar Kehadiran')
                    ->helperText('Pilih kelas lalu klik tombol muat semua siswa untuk mengisi daftar dengan cepat.')
                    ->schema([
                        Select::make('student_id')
                            ->label('Nama Siswa')
                            ->searchable()
                            ->preload()
                            ->options(function () {
                                $classId = $this->data['class_id'] ?? null;
                                if (! $classId) {
                                    return [];
                                }

                                return Student::where('class_id', $classId)
                                    ->where('status', 'active')
                                    ->orderBy('name')
                                    ->pluck('name', 'id');
                            })
                            ->required()
                            ->columnSpan(2),

                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'present'    => '🟢 Hadir',
                                'late'       => '🟡 Terlambat',
                                'sick'       => '🔵 Sakit',
                                'permission' => '🟡 Izin',
                                'absent'     => '🔴 Alpha',
                            ])
                            ->default('present')
                            ->required()
                            ->columnSpan(1),

                        \Filament\Forms\Components\TextInput::make('note')
                            ->label('Catatan')
                            ->placeholder('Masukkan catatan jika ada (opsional)...')
                            ->maxLength(255)
                            ->columnSpan(3),
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

        $this->data['attendances'] = $students->map(fn($s) => [
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
        $teacherId = $teacher?->id;

        if (! $teacherId) {
            // Resolve from the selected presensi session (e.g. if the user is an admin)
            if (!empty($data['presensi_session_id'])) {
                $session = PresensiSession::find($data['presensi_session_id']);
                $teacherId = $session?->teacher_id;
            }
        }

        if (! $teacherId) {
            Notification::make()
                ->title('Guru pengampu tidak ditemukan')
                ->body('Pastikan Anda memilih sesi presensi yang benar atau akun Anda terdaftar sebagai guru.')
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
            $teacherId,
            $data['class_id'],
            $data['date'],
            $data['attendances'],
            $data['presensi_session_id'] ?? null
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
