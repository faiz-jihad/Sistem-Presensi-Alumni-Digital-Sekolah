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
use Filament\Resources\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use App\Filament\Resources\StudentAttendances\StudentAttendanceResource;

class ManualAttendance extends Page
{
    protected static string $resource = StudentAttendanceResource::class;

    protected string $view = 'filament.resources.student-attendances.pages.manual-attendance';

    protected static ?string $title = 'Presensi Manual Kelas';

    protected static ?string $navigationLabel = 'Presensi Manual Kelas';

    protected static string|\UnitEnum|null $navigationGroup = 'Presensi';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-pencil-square';

    protected static ?int $navigationSort = 2;

    public static function shouldRegisterNavigation(array $parameters = []): bool
    {
        return in_array(auth()->user()->role, ['super_admin', 'admin', 'teacher'])
            && auth()->user()->hasFeature('has_presensi')
            && auth()->user()->school?->status === 'active';
    }

    public ?array $data = [];

    public array $attendances = [];

    public array $studentList = [];

    public function mount(): void
    {
        $sessionId = request()->query('session_id');
        $session = $sessionId ? PresensiSession::with('schedule')->find($sessionId) : null;

        $this->data = [
            'date'                => $session ? $session->date : now()->toDateString(),
            'class_id'            => $session ? $session->schedule?->class_id : null,
            'presensi_session_id' => $session ? $session->id : null,
        ];

        $this->attendances = [];
        $this->studentList = [];

        if ($session && $session->schedule?->class_id) {
            $students = Student::where('class_id', $session->schedule->class_id)
                ->where('status', 'active')
                ->with(['attendances' => function ($query) {
                    $query->latest('date')->limit(4);
                }])
                ->orderBy('name')
                ->get();

            $this->studentList = $students->map(fn($s) => [
                'id' => $s->id,
                'name' => $s->name,
                'nis' => $s->nis,
                'nisn' => $s->nisn,
                'history' => $s->attendances->sortBy('date')->map(fn($a) => [
                    'status' => $a->status instanceof \App\Enums\AttendanceStatus ? $a->status->value : $a->status,
                    'date' => $a->date,
                ])->values()->toArray(),
            ])->toArray();

            // Load existing attendances if they exist
            $existingAttendances = StudentAttendance::where('presensi_session_id', $session->id)->get();

            if ($existingAttendances->isNotEmpty()) {
                $this->attendances = $existingAttendances->map(fn($att) => [
                    'student_id' => $att->student_id,
                    'status'     => $att->status instanceof AttendanceStatus ? $att->status->value : $att->status,
                    'note'       => $att->note,
                ])->toArray();
            } else {
                $this->attendances = $students->map(fn($s) => [
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
                    ->options(function () {
                        $user = auth()->user();
                        $query = StudentClass::query()->orderBy('name');
                        if ($user->role !== 'super_admin' && $user->school_id) {
                            $query->where('school_id', $user->school_id);
                        }
                        return $query->pluck('name', 'id');
                    })
                    ->required()
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(function ($state, $livewire) {
                        if ($state) {
                            $students = Student::where('class_id', $state)
                                ->where('status', 'active')
                                ->with(['attendances' => function ($query) {
                                    $query->latest('date')->limit(4);
                                }])
                                ->orderBy('name')
                                ->get();

                            $livewire->studentList = $students->map(fn($s) => [
                                'id' => $s->id,
                                'name' => $s->name,
                                'nis' => $s->nis,
                                'nisn' => $s->nisn,
                                'history' => $s->attendances->sortBy('date')->map(fn($a) => [
                                    'status' => $a->status instanceof \App\Enums\AttendanceStatus ? $a->status->value : $a->status,
                                    'date' => $a->date,
                                ])->values()->toArray(),
                            ])->toArray();

                            $livewire->attendances = $students->map(fn($s) => [
                                'student_id' => $s->id,
                                'status'     => 'present',
                                'note'       => '',
                            ])->toArray();
                        } else {
                            $livewire->studentList = [];
                            $livewire->attendances = [];
                        }
                    }),

                DatePicker::make('date')
                    ->label('Tanggal')
                    ->required()
                    ->default(now()),

            ])
            ->statePath('data');
    }

    /**
     * Auto-populate dengan semua siswa aktif di kelas
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
            ->with(['attendances' => function ($query) {
                $query->latest('date')->limit(4);
            }])
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

        $this->studentList = $students->map(fn($s) => [
            'id' => $s->id,
            'name' => $s->name,
            'nis' => $s->nis,
            'nisn' => $s->nisn,
            'history' => $s->attendances->sortBy('date')->map(fn($a) => [
                'status' => $a->status instanceof \App\Enums\AttendanceStatus ? $a->status->value : $a->status,
                'date' => $a->date,
            ])->values()->toArray(),
        ])->toArray();

        $this->attendances = $students->map(fn($s) => [
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

        if (! $teacherId && !empty($data['class_id'])) {
            // Fallback: Wali kelas (homeroom teacher)
            $teacherId = \Illuminate\Support\Facades\DB::table('classes')
                ->where('id', $data['class_id'])
                ->value('homeroom_teacher_id');
        }

        if (empty($this->attendances)) {
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
            $this->attendances,
            null // No presensiSessionId
        );

        Notification::make()
            ->title('Presensi berhasil disimpan')
            ->body('Total ' . $result['count'] . ' siswa berhasil direkap. Notifikasi WhatsApp dikirim ke orang tua.')
            ->success()
            ->send();

        // Reset setelah submit
        $this->attendances = [];
        $this->studentList = [];

        // Redirect ke daftar presensi
        $this->redirect(StudentAttendanceResource::getUrl('index'));
    }
}
