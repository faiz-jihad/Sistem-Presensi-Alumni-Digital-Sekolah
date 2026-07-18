<?php

namespace App\Services;

use App\Models\PrayerAttendance;
use App\Models\Student;
use App\Models\StudentClass;
use App\Models\Teacher;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class PrayerAttendanceService
{
    public const PRAYER_TYPES = ['subuh', 'dzuhur', 'ashar', 'maghrib', 'isya'];

    public function __construct(private readonly PrayerScheduleService $scheduleService) {}

    /** @return array<string, mixed> */
    public function todaySummary(User $user): array
    {
        $student = $this->resolveStudent($user);
        $now = now();
        $schedule = $this->scheduleService->forDate($student->school, $now);
        $existing = PrayerAttendance::query()
            ->where('student_id', $student->id)
            ->whereDate('attendance_date', $now->toDateString())
            ->get()
            ->keyBy('prayer_type');

        $items = collect(self::PRAYER_TYPES)->map(function (string $type) use ($existing, $now, $schedule): array {
            $attendance = $existing->get($type);
            $window = $this->submissionWindow($now, $schedule['times'][$type]);

            if ($attendance) {
                return [
                    'prayer_type' => $type,
                    'status' => $attendance->status,
                    'is_enabled' => true,
                    'can_submit' => false,
                    'scheduled_at' => $schedule['times'][$type],
                    'window_label' => $window['label'],
                    'submitted_at' => $attendance->submitted_at?->toIso8601String(),
                    'verified_at' => $attendance->verified_at?->toIso8601String(),
                    'rejection_reason' => $attendance->status === 'rejected'
                        ? $attendance->teacher_note
                        : null,
                    'resubmission_allowed' => false,
                ];
            }

            return [
                'prayer_type' => $type,
                'status' => $window['status'],
                'is_enabled' => true,
                'can_submit' => $window['can_submit'],
                'scheduled_at' => $schedule['times'][$type],
                'window_label' => $window['label'],
                'submitted_at' => null,
                'verified_at' => null,
                'rejection_reason' => null,
                'resubmission_allowed' => false,
            ];
        })->values()->all();

        return [
            'date' => $now->toDateString(),
            'location' => [
                'province' => $schedule['province'],
                'city' => $schedule['city'],
            ],
            'items' => $items,
        ];
    }

    public function submit(User $user, string $prayerType): PrayerAttendance
    {
        if (! in_array($prayerType, self::PRAYER_TYPES, true)) {
            throw new RuntimeException('Jenis sholat tidak valid.');
        }

        $student = $this->resolveStudent($user);
        $now = now();
        $schedule = $this->scheduleService->forDate($student->school, $now);
        $window = $this->submissionWindow($now, $schedule['times'][$prayerType]);

        if (! $window['can_submit']) {
            throw new RuntimeException(
                $window['status'] === 'not_available'
                    ? 'Waktu presensi sholat ini belum dimulai.'
                    : 'Waktu presensi sholat ini sudah berakhir.'
            );
        }

        return DB::transaction(function () use ($student, $prayerType, $now, $schedule, $window): PrayerAttendance {
            $alreadyExists = PrayerAttendance::query()
                ->where('student_id', $student->id)
                ->whereDate('attendance_date', $now->toDateString())
                ->where('prayer_type', $prayerType)
                ->lockForUpdate()
                ->exists();

            if ($alreadyExists) {
                throw new RuntimeException('Anda sudah mengirim presensi sholat ini.');
            }

            return PrayerAttendance::create([
                'school_id' => $student->school_id,
                'class_id' => $student->class_id,
                'student_id' => $student->id,
                'prayer_type' => $prayerType,
                'attendance_date' => $now->toDateString(),
                'scheduled_at' => $schedule['times'][$prayerType].':00',
                'submitted_at' => $now,
                'status' => $window['is_late'] ? 'late' : 'pending',
            ])->load(['student', 'studentClass', 'verifier']);
        });
    }

    /** @return Collection<int, PrayerAttendance> */
    public function pendingForTeacher(User $user): Collection
    {
        $classIds = $this->teacherClassIds($user);

        return PrayerAttendance::query()
            ->with(['student', 'studentClass', 'verifier'])
            ->whereIn('class_id', $classIds)
            ->whereIn('status', ['pending', 'late'])
            ->latest('submitted_at')
            ->get();
    }

    public function verify(
        User $user,
        PrayerAttendance $attendance,
        bool $approved,
        ?string $note = null
    ): PrayerAttendance {
        $this->assertTeacherCanAccess($user, $attendance);

        if (! in_array($attendance->status, ['pending', 'late'], true)) {
            throw new RuntimeException('Presensi ini sudah diproses sebelumnya.');
        }

        $attendance->update([
            'status' => $approved ? 'approved' : 'rejected',
            'verified_by' => $user->id,
            'verified_at' => now(),
            'teacher_note' => filled($note) ? trim($note) : null,
        ]);

        return $attendance->fresh(['student', 'studentClass', 'verifier']);
    }

    /** @param array<int> $attendanceIds */
    public function verifyAll(User $user, array $attendanceIds, ?string $note = null): Collection
    {
        $classIds = $this->teacherClassIds($user);
        $attendances = PrayerAttendance::query()
            ->whereIn('id', $attendanceIds)
            ->whereIn('class_id', $classIds)
            ->whereIn('status', ['pending', 'late'])
            ->get();

        if ($attendances->count() !== count(array_unique($attendanceIds))) {
            throw new RuntimeException('Sebagian data presensi tidak tersedia atau tidak dapat diakses.');
        }

        DB::transaction(function () use ($attendances, $user, $note): void {
            foreach ($attendances as $attendance) {
                $attendance->update([
                    'status' => 'approved',
                    'verified_by' => $user->id,
                    'verified_at' => now(),
                    'teacher_note' => filled($note) ? trim($note) : null,
                ]);
            }
        });

        return PrayerAttendance::query()
            ->with(['student', 'studentClass', 'verifier'])
            ->whereIn('id', $attendanceIds)
            ->get();
    }

    /** @param array<string, mixed> $filters */
    public function history(User $user, array $filters = []): Collection
    {
        $query = PrayerAttendance::query()->with(['student', 'studentClass', 'verifier']);

        if ($user->role === 'student') {
            $query->where('student_id', $this->resolveStudent($user)->id);
        } elseif ($user->role === 'teacher') {
            $query->whereIn('class_id', $this->teacherClassIds($user));
        } else {
            throw new RuntimeException('Role ini tidak dapat mengakses riwayat presensi sholat.');
        }

        $this->applyHistoryFilters($query, $filters);

        return $query
            ->orderByDesc('attendance_date')
            ->orderByRaw("CASE prayer_type WHEN 'subuh' THEN 1 WHEN 'dzuhur' THEN 2 WHEN 'ashar' THEN 3 WHEN 'maghrib' THEN 4 ELSE 5 END")
            ->get();
    }

    public function detail(User $user, PrayerAttendance $attendance): PrayerAttendance
    {
        if ($user->role === 'student') {
            if ($attendance->student_id !== $this->resolveStudent($user)->id) {
                throw new RuntimeException('Anda tidak dapat mengakses riwayat ini.');
            }
        } elseif ($user->role === 'teacher') {
            $this->assertTeacherCanAccess($user, $attendance);
        } else {
            throw new RuntimeException('Role ini tidak dapat mengakses riwayat presensi sholat.');
        }

        return $attendance->load(['student', 'studentClass', 'verifier']);
    }

    public function resolveStudent(User $user): Student
    {
        if ($user->role !== 'student' || ! $user->school_id) {
            throw new RuntimeException('Akun ini bukan akun siswa yang valid.');
        }

        $identifiers = array_values(array_unique(array_filter([
            $user->email,
            Str::before($user->email, '@'),
        ])));

        $student = Student::query()
            ->with('school')
            ->where('school_id', $user->school_id)
            ->where('status', 'active')
            ->where(function (Builder $query) use ($identifiers, $user): void {
                $query->whereIn('nis', $identifiers)
                    ->orWhere('name', $user->name);
            })
            ->first();

        if (! $student) {
            throw new RuntimeException('Akun Anda belum terhubung dengan data siswa.');
        }

        return $student;
    }

    /** @return array<int> */
    public function teacherClassIds(User $user): array
    {
        if ($user->role !== 'teacher') {
            throw new RuntimeException('Akun ini bukan akun guru.');
        }

        $teacher = Teacher::query()
            ->where('user_id', $user->id)
            ->where('school_id', $user->school_id)
            ->first();

        if (! $teacher) {
            throw new RuntimeException('Akun guru belum terhubung dengan data guru.');
        }

        return StudentClass::query()
            ->where('school_id', $user->school_id)
            ->where('homeroom_teacher_id', $teacher->id)
            ->where('status', 'active')
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->all();
    }

    /** @return array{status:string, can_submit:bool, is_late:bool, label:string} */
    private function submissionWindow(Carbon $now, string $scheduledTime): array
    {
        $scheduledAt = Carbon::parse($now->toDateString().' '.$scheduledTime, config('app.timezone'));
        $onTimeEnd = $scheduledAt->copy()->addMinutes((int) config('services.equran_prayer.on_time_minutes', 60));
        $lateEnd = $onTimeEnd->copy()->addMinutes((int) config('services.equran_prayer.late_minutes', 30));

        $status = match (true) {
            $now->lt($scheduledAt) => 'not_available',
            $now->lte($lateEnd) => 'open',
            default => 'expired',
        };

        return [
            'status' => $status,
            'can_submit' => $status === 'open',
            'is_late' => $now->gt($onTimeEnd) && $now->lte($lateEnd),
            'label' => $scheduledAt->format('H:i').' - '.$lateEnd->format('H:i').' WIB',
        ];
    }

    private function assertTeacherCanAccess(User $user, PrayerAttendance $attendance): void
    {
        if (! in_array($attendance->class_id, $this->teacherClassIds($user), true)) {
            throw new RuntimeException('Anda tidak memiliki akses ke presensi siswa ini.');
        }
    }

    /** @param array<string, mixed> $filters */
    private function applyHistoryFilters(Builder $query, array $filters): void
    {
        $query
            ->when($filters['from_date'] ?? null, fn (Builder $builder, string $date) => $builder->whereDate('attendance_date', '>=', $date))
            ->when($filters['to_date'] ?? null, fn (Builder $builder, string $date) => $builder->whereDate('attendance_date', '<=', $date))
            ->when($filters['class_id'] ?? null, fn (Builder $builder, $classId) => $builder->where('class_id', $classId))
            ->when($filters['prayer_type'] ?? null, fn (Builder $builder, string $type) => $builder->where('prayer_type', $type))
            ->when($filters['status'] ?? null, fn (Builder $builder, string $status) => $builder->where('status', $status))
            ->when($filters['search'] ?? null, function (Builder $builder, string $search): void {
                $builder->whereHas('student', function (Builder $studentQuery) use ($search): void {
                    $studentQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('nis', 'like', "%{$search}%");
                });
            });
    }
}
