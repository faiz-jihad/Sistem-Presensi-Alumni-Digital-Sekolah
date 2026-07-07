<?php

namespace App\Services;

use App\Enums\AttendanceStatus;
use App\Enums\DayOfWeek;
use App\Enums\SessionStatus;
use App\Models\PresensiSession;
use App\Models\QrToken;
use App\Models\Schedule;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\Teacher;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Jobs\SendWhatsAppNotification;

class PresensiSessionService
{
    /* ═══════════════════════════════════════════════
     *  LIST / FILTER
     * ═══════════════════════════════════════════════ */

    public function listForUser($user, array $filters = [])
    {
        $query = PresensiSession::with([
            'schedule.class',
            'schedule.subject',
            'schedule.classHour',
            'teacher',
        ]);

        if ($user->role === 'teacher') {
            $teacher = Teacher::where('user_id', $user->id)->first();
            if (!$teacher) {
                throw new \Exception('Data guru tidak ditemukan untuk akun ini.', 404);
            }
            $query->where('teacher_id', $teacher->id);
        } elseif (in_array($user->role, ['admin', 'super_admin'], true)) {
            if ($user->school_id) {
                $query->where('school_id', $user->school_id);
            }
        } else {
            throw new \Exception('Anda tidak memiliki akses ke sesi presensi.', 403);
        }

        if (!empty($filters['date']))        $query->where('date', $filters['date']);
        if (!empty($filters['status']))      $query->where('status', $filters['status']);
        if (!empty($filters['schedule_id'])) $query->where('schedule_id', $filters['schedule_id']);

        return $query->latest('date')->latest('id')->get();
    }

    /* ═══════════════════════════════════════════════
     *  JADWAL HARI INI (TEACHER DASHBOARD)
     * ═══════════════════════════════════════════════ */

    /**
     * Ambil jadwal hari ini untuk seorang guru (dengan dukungan simulasi hari).
     * 1. Sedang berlangsung (open)
     * 2. Akan dimulai (upcoming / belum dibuka)
     * 3. Sudah selesai (closed / missed)
     */
    public function getTodaySchedulesForTeacher(Teacher $teacher, ?string $dayOverride = null): Collection
    {
        $today     = Carbon::today();
        $todayDate = $today->toDateString();
        $now       = Carbon::now();

        // Resolusi hari: gunakan override jika ada, jika tidak gunakan hari ini.
        // Jika hari ini akhir pekan (Sabtu/Minggu), default ke hari Senin agar user bisa mengetes.
        $resolvedDay = DayOfWeek::fromCarbon($today);
        if ($dayOverride) {
            $resolvedDay = DayOfWeek::tryFrom($dayOverride) ?? $resolvedDay;
        } elseif (in_array($resolvedDay, [DayOfWeek::Saturday, DayOfWeek::Sunday], true)) {
            $resolvedDay = DayOfWeek::Monday;
        }

        $schedules = Schedule::with([
            'class',
            'subject',
            'classHour',
            'presensiSessions' => fn ($q) => $q->where('date', $todayDate),
        ])
            ->where('teacher_id', $teacher->id)
            ->where('day', $resolvedDay->value)
            ->where('is_active', true)
            ->get();

        return $schedules->map(function (Schedule $schedule) use ($todayDate, $now, $teacher) {
            $session   = $schedule->presensiSessions->first();
            $classHour = $schedule->classHour;

            $startTime = $classHour?->start_time;
            $endTime   = $classHour?->end_time;

            $startCarbon = $startTime ? Carbon::parse($todayDate . ' ' . $startTime) : null;
            $endCarbon   = $endTime   ? Carbon::parse($todayDate . ' ' . $endTime)   : null;

            $displayStatus = $this->resolveDisplayStatus($session, $now, $startCarbon, $endCarbon, $schedule);

            return [
                'schedule_id'       => $schedule->id,
                'class'             => [
                    'id'   => $schedule->class?->id,
                    'name' => $schedule->class?->name,
                ],
                'subject'           => [
                    'id'   => $schedule->subject?->id,
                    'name' => $schedule->subject?->name,
                ],
                'start_time'        => $startTime,
                'end_time'          => $endTime,
                'room'              => $schedule->room,
                'allow_early_open'  => $schedule->allow_early_open,
                'status'            => $displayStatus,
                'session'           => $session ? [
                    'id'         => $session->id,
                    'status'     => $session->status->value,
                    'status_label' => $session->status->label(),
                    'opened_at'  => $session->opened_at?->toTimeString(),
                    'closed_at'  => $session->closed_at?->toTimeString(),
                ] : null,
            ];
        })->sortBy(function (array $item) {
            return match ($item['status']) {
                'teaching'  => 0,
                'eligible'  => 1,
                'upcoming'  => 2,
                'completed' => 3,
                'missed'    => 4,
                default     => 5,
            };
        })->values();
    }

    private function resolveDisplayStatus(
        ?PresensiSession $session,
        Carbon $now,
        ?Carbon $startCarbon,
        ?Carbon $endCarbon,
        Schedule $schedule
    ): string {
        if ($session) {
            return match ($session->status) {
                SessionStatus::Open      => 'teaching',
                SessionStatus::Closed    => 'completed',
                SessionStatus::Cancelled => 'cancelled',
                default                  => 'upcoming',
            };
        }

        if ($endCarbon && $now->greaterThan($endCarbon)) {
            return 'missed';
        }

        if ($startCarbon) {
            $allowedOpen = $schedule->allow_early_open
                ? $startCarbon->copy()->subMinutes(30)
                : $startCarbon;

            if ($now->greaterThanOrEqualTo($allowedOpen)) {
                return 'eligible'; // Bisa dibuka
            }
        }

        return 'upcoming';
    }

    /* ═══════════════════════════════════════════════
     *  RIWAYAT PRESENSI GURU
     * ═══════════════════════════════════════════════ */

    public function getHistory(Teacher $teacher, array $filters = []): Collection
    {
        return PresensiSession::with([
            'schedule.class',
            'schedule.subject',
            'schedule.classHour',
            'studentAttendances',
        ])
            ->where('teacher_id', $teacher->id)
            ->when(!empty($filters['date']), fn ($q) => $q->where('date', $filters['date']))
            ->when(!empty($filters['status']), fn ($q) => $q->where('status', $filters['status']))
            ->latest('date')
            ->latest('id')
            ->get();
    }

    /* ═══════════════════════════════════════════════
     *  BUKA KELAS
     * ═══════════════════════════════════════════════ */

    /**
     * Buka kelas berdasarkan schedule_id.
     * Jika sesi belum ada untuk hari ini, buat baru.
     * Jika sudah ada, ubah statusnya ke open.
     */
    public function openBySchedule(int $scheduleId, int $userId): PresensiSession
    {
        $schedule = Schedule::with(['classHour', 'class', 'subject'])->findOrFail($scheduleId);
        $today    = Carbon::today()->toDateString();
        $teacher  = Teacher::where('user_id', $userId)->firstOrFail();

        // Validasi jadwal milik guru ini
        if ((int) $schedule->teacher_id !== (int) $teacher->id) {
            throw new \Exception('Jadwal ini bukan milik Anda.', 403);
        }

        // Pastikan tidak ada sesi lain yang OPEN untuk jadwal ini
        $existingOpen = PresensiSession::where('schedule_id', $scheduleId)
            ->where('date', $today)
            ->where('status', SessionStatus::Open->value)
            ->first();

        if ($existingOpen) {
            throw new \Exception('Sesi presensi untuk jadwal ini sudah dibuka.', 422);
        }

        return DB::transaction(function () use ($schedule, $today, $userId, $teacher) {
            $session = PresensiSession::firstOrCreate(
                [
                    'schedule_id' => $schedule->id,
                    'date'        => $today,
                ],
                [
                    'school_id'   => $schedule->school_id,
                    'teacher_id'  => $teacher->id,
                    'start_time'  => $schedule->classHour?->start_time,
                    'end_time'    => $schedule->classHour?->end_time,
                    'status'      => SessionStatus::Scheduled->value,
                ]
            );

            if (in_array($session->status, [SessionStatus::Closed, SessionStatus::Cancelled], true)) {
                throw new \Exception('Sesi yang sudah ditutup atau dibatalkan tidak dapat dibuka kembali.', 422);
            }

            $session->update([
                'status'    => SessionStatus::Open->value,
                'opened_by' => $userId,
                'opened_at' => Carbon::now(),
            ]);

            // Muat relasi untuk notifikasi
            $session->load(['teacher', 'schedule.class', 'schedule.subject']);

            // Kirim notifikasi database ke Admin & Super Admin
            $admins = \App\Models\User::role(['admin', 'super_admin'])->get();
            if ($admins->isNotEmpty()) {
                $teacherName = $session->teacher?->name ?? 'Guru';
                $className = $session->schedule?->class?->name ?? 'Kelas';
                $subjectName = $session->schedule?->subject?->name ?? 'Mata Pelajaran';
                
                \Filament\Notifications\Notification::make()
                    ->title('Sesi Kelas Dibuka')
                    ->body("Guru **{$teacherName}** telah membuka sesi kelas **{$className}** untuk pelajaran **{$subjectName}**.")
                    ->success()
                    ->sendToDatabase($admins);
            }

            return $session->refresh();
        });
    }

    /* ═══════════════════════════════════════════════
     *  TUTUP KELAS
     * ═══════════════════════════════════════════════ */

    public function closeSession(PresensiSession $session, int $userId): PresensiSession
    {
        if ($session->status !== SessionStatus::Open) {
            throw new \Exception('Hanya sesi yang sedang dibuka yang dapat ditutup.', 422);
        }

        DB::transaction(function () use ($session, $userId) {
            $session->update([
                'status'    => SessionStatus::Closed->value,
                'closed_by' => $userId,
                'closed_at' => Carbon::now(),
            ]);

            // Automatically record checkout time for present and late students in this session
            $nowTime = Carbon::now()->toTimeString();
            StudentAttendance::where('presensi_session_id', $session->id)
                ->whereIn('status', [AttendanceStatus::Present->value, AttendanceStatus::Late->value])
                ->whereNull('check_out_time')
                ->update([
                    'check_out_time' => $nowTime,
                ]);
        });

        // Muat relasi untuk notifikasi
        $session->load(['teacher', 'schedule.class', 'schedule.subject']);

        // Kirim notifikasi database ke Admin & Super Admin
        $admins = \App\Models\User::role(['admin', 'super_admin'])->get();
        if ($admins->isNotEmpty()) {
            $teacherName = $session->teacher?->name ?? 'Guru';
            $className = $session->schedule?->class?->name ?? 'Kelas';
            $subjectName = $session->schedule?->subject?->name ?? 'Mata Pelajaran';
            
            \Filament\Notifications\Notification::make()
                ->title('Sesi Kelas Ditutup')
                ->body("Guru **{$teacherName}** telah menutup sesi kelas **{$className}** untuk pelajaran **{$subjectName}**.")
                ->info()
                ->sendToDatabase($admins);
        }

        return $session->refresh();
    }

    /* ═══════════════════════════════════════════════
     *  PRESENSI MANUAL
     * ═══════════════════════════════════════════════ */

    /**
     * Simpan presensi manual untuk semua siswa dalam satu sesi.
     */
    public function saveManualAttendance(PresensiSession $session, array $attendances): array
    {
        if ($session->status !== SessionStatus::Open) {
            throw new \Exception('Presensi hanya dapat dilakukan pada sesi yang sedang berlangsung.', 422);
        }

        $schedule = $session->schedule()->with('class')->first();
        $classId  = $schedule?->class_id;

        if (!$classId) {
            throw new \Exception('Kelas tidak ditemukan pada jadwal ini.', 422);
        }

        $recordedCount = 0;

        DB::transaction(function () use ($session, $attendances, $classId, $schedule, &$recordedCount) {
            foreach ($attendances as $att) {
                $studentId = $att['student_id'] ?? null;
                $status    = $att['status'] ?? AttendanceStatus::Present->value;
                $note      = $att['note'] ?? null;

                if (!$studentId) continue;

                // Validasi siswa ada di kelas ini
                $student = Student::where('id', $studentId)
                    ->where('class_id', $classId)
                    ->first();

                if (!$student) continue;

                $attendance = StudentAttendance::updateOrCreate(
                    [
                        'student_id'          => $studentId,
                        'presensi_session_id' => $session->id,
                    ],
                    [
                        'school_id'     => $student->school_id,
                        'class_id'      => $classId,
                        'teacher_id'    => $session->teacher_id,
                        'date'          => $session->date,
                        'status'        => $status,
                        'note'          => $note,
                        'check_in_time' => in_array($status, [
                            AttendanceStatus::Present->value,
                            AttendanceStatus::Late->value,
                        ], true) ? ($att['check_in_time'] ?? Carbon::now()->toTimeString()) : null,
                    ]
                );

                $recordedCount++;
                
                // Memicu notifikasi WhatsApp ke Orang Tua
                $this->triggerWhatsAppNotification($student, $attendance);
            }
        });

        return [
            'success'   => true,
            'session_id' => $session->id,
            'count'     => $recordedCount,
        ];
    }

    /* ═══════════════════════════════════════════════
     *  QR TOKEN
     * ═══════════════════════════════════════════════ */

    /**
     * Generate QR token untuk sesi (berlaku 5 menit).
     */
    public function generateQrToken(PresensiSession $session): QrToken
    {
        if ($session->status !== SessionStatus::Open) {
            throw new \Exception('QR hanya dapat dibuat untuk sesi yang sedang berlangsung.', 422);
        }

        // Invalidasi token lama yang belum digunakan
        QrToken::where('presensi_session_id', $session->id)
            ->where('used', false)
            ->update(['expired_at' => Carbon::now()]);

        return QrToken::create([
            'presensi_session_id' => $session->id,
            'token'               => Str::random(40),
            'expired_at'          => Carbon::now()->addMinutes(5),
            'used'                => false,
        ]);
    }

    /**
     * Scan QR token — catat presensi siswa.
     */
    public function scanQrToken(string $token, Student $student): StudentAttendance
    {
        $qrToken = QrToken::with([
            'presensiSession.schedule.class',
            'presensiSession.schedule.classHour',
        ])->where('token', $token)->firstOrFail();

        // Validasi token masih valid
        if (!$qrToken->isValid()) {
            throw new \Exception('Token QR sudah kedaluwarsa atau telah digunakan.', 422);
        }

        $session = $qrToken->presensiSession;

        // Validasi sesi masih OPEN
        if ($session->status !== SessionStatus::Open) {
            throw new \Exception('Sesi presensi ini sudah ditutup.', 422);
        }

        // Validasi siswa dari kelas yang benar
        $classId = $session->schedule?->class_id;
        if ($classId && (int) $student->class_id !== (int) $classId) {
            throw new \Exception('Anda tidak terdaftar di kelas untuk sesi presensi ini.', 403);
        }

        // Cek apakah siswa sudah presensi pada sesi ini
        $existing = StudentAttendance::where('student_id', $student->id)
            ->where('presensi_session_id', $session->id)
            ->first();

        if ($existing) {
            throw new \Exception('Anda sudah melakukan presensi pada sesi ini.', 422);
        }

        $now          = Carbon::now();
        $startTimeRaw = $session->start_time ?? $session->schedule?->classHour?->start_time ?? '07:00:00';
        $startCarbon  = Carbon::parse($startTimeRaw);
        $diffMinutes  = $startCarbon->diffInMinutes($now, false);

        $status = $diffMinutes > 15 ? AttendanceStatus::Late : AttendanceStatus::Present;
        $note   = $status === AttendanceStatus::Late
            ? "Terlambat scan QR ({$diffMinutes} menit)"
            : 'Scan QR tepat waktu';

        return DB::transaction(function () use ($qrToken, $session, $student, $now, $status, $note) {
            // Tandai token sebagai used
            $qrToken->markAsUsed();

            $attendance = StudentAttendance::create([
                'school_id'           => $student->school_id,
                'class_id'            => $student->class_id,
                'student_id'          => $student->id,
                'teacher_id'          => $session->teacher_id,
                'presensi_session_id' => $session->id,
                'date'                => $session->date,
                'status'              => $status->value,
                'check_in_time'       => $now->toTimeString(),
                'note'                => $note,
            ]);

            // Memicu notifikasi WhatsApp ke Orang Tua
            $this->triggerWhatsAppNotification($student, $attendance);

            // Memicu notifikasi database ke guru pengajar
            $teacherUser = $session->teacher?->user;
            if ($teacherUser) {
                $className = $session->schedule?->class?->name ?? 'Kelas';
                $statusLabel = $status->value === AttendanceStatus::Late->value ? 'Terlambat' : 'Hadir';
                
                $dbNotif = \Filament\Notifications\Notification::make()
                    ->title('Siswa Scan QR')
                    ->body("**{$student->name}** melakukan scan QR di kelas **{$className}** dengan status **{$statusLabel}**.");
                
                if ($status->value === AttendanceStatus::Late->value) {
                    $dbNotif->warning();
                } else {
                    $dbNotif->success();
                }
                
                $dbNotif->sendToDatabase($teacherUser);
            }

            return $attendance;
        });
    }

    /* ═══════════════════════════════════════════════
     *  CRUD UMUM (dipakai Filament / admin)
     * ═══════════════════════════════════════════════ */

    public function create(array $data, ?int $teacherId = null): PresensiSession
    {
        if (empty($data['schedule_id'])) {
            throw new \Exception('Jadwal pelajaran wajib dipilih.');
        }

        $schedule  = Schedule::with('classHour')->findOrFail($data['schedule_id']);
        $date      = $this->normalizeDate($data['date'] ?? Carbon::today());
        $status    = $data['status'] ?? SessionStatus::Scheduled->value;
        $startTime = $this->normalizeTime($data['start_time'] ?? $schedule->classHour?->start_time);
        $endTime   = $this->normalizeTime($data['end_time'] ?? $schedule->classHour?->end_time);

        $session = PresensiSession::where('schedule_id', $schedule->id)
            ->where('date', $date)
            ->first();

        $values = [
            'school_id'      => $schedule->school_id,
            'schedule_id'    => $schedule->id,
            'teacher_id'     => $data['teacher_id'] ?? $teacherId ?? $schedule->teacher_id,
            'date'           => $date,
            'start_time'     => $startTime,
            'end_time'       => $endTime,
            'status'         => $status,
            'material_topic' => $data['material_topic'] ?? null,
            'notes'          => $data['notes'] ?? null,
        ];

        if ($session) {
            $session->update($values);
            return $session->refresh();
        }

        return PresensiSession::create($values);
    }

    public function update(PresensiSession $session, array $data): PresensiSession
    {
        $schedule  = Schedule::with('classHour')->findOrFail($data['schedule_id'] ?? $session->schedule_id);
        $date      = $this->normalizeDate($data['date'] ?? $session->date);
        $startTime = $this->normalizeTime($data['start_time'] ?? $session->start_time ?? $schedule->classHour?->start_time);
        $endTime   = $this->normalizeTime($data['end_time'] ?? $session->end_time ?? $schedule->classHour?->end_time);

        $session->update([
            'school_id'      => $schedule->school_id,
            'schedule_id'    => $schedule->id,
            'teacher_id'     => $data['teacher_id'] ?? $session->teacher_id ?? $schedule->teacher_id,
            'date'           => $date,
            'start_time'     => $startTime,
            'end_time'       => $endTime,
            'status'         => $data['status'] ?? $session->status->value,
            'material_topic' => array_key_exists('material_topic', $data) ? $data['material_topic'] : $session->material_topic,
            'notes'          => array_key_exists('notes', $data) ? $data['notes'] : $session->notes,
        ]);

        return $session->refresh();
    }

    public function open(PresensiSession $session): PresensiSession
    {
        if (in_array($session->status, [SessionStatus::Closed, SessionStatus::Cancelled], true)) {
            throw new \Exception('Sesi yang sudah ditutup atau dibatalkan tidak dapat dibuka kembali.', 422);
        }

        $session->update([
            'status'    => SessionStatus::Open->value,
            'opened_at' => $session->opened_at ?? Carbon::now(),
        ]);

        return $session->refresh();
    }

    public function close(PresensiSession $session, int $userId): PresensiSession
    {
        return $this->closeSession($session, $userId);
    }

    public function delete(PresensiSession $session): void
    {
        if ($session->studentAttendances()->exists()) {
            throw new \Exception('Sesi tidak dapat dihapus karena sudah memiliki data presensi siswa.');
        }
        $session->delete();
    }

    public function canShowQr(PresensiSession $session): bool
    {
        return $session->status === SessionStatus::Open
            && $session->date === Carbon::today()->toDateString();
    }

    public function ensureUserCanManageSession($user, PresensiSession $session): void
    {
        if ($user->role === 'teacher') {
            $teacher = Teacher::where('user_id', $user->id)->first();
            if (!$teacher) {
                throw new \Exception('Data guru tidak ditemukan untuk akun ini.', 403);
            }
            if ($session->teacher_id !== null && (int) $session->teacher_id !== (int) $teacher->id) {
                throw new \Exception('Anda tidak berhak mengelola sesi presensi ini.', 403);
            }
        }

        if (
            in_array($user->role, ['admin', 'super_admin'], true)
            && $user->school_id
            && $session->school_id !== $user->school_id
        ) {
            throw new \Exception('Anda tidak berhak mengelola sesi presensi sekolah lain.', 403);
        }
    }

    /* ═══════════════════════════════════════════════
     *  PRIVATE HELPERS
     * ═══════════════════════════════════════════════ */

    private function normalizeDate($date): string
    {
        return Carbon::parse($date)->toDateString();
    }

    private function normalizeTime(?string $time): ?string
    {
        return $time ? Carbon::parse($time)->format('H:i:s') : null;
    }

    /**
     * Memicu pengiriman notifikasi WhatsApp ke Orang Tua berdasarkan status kehadiran.
     */
    private function triggerWhatsAppNotification(Student $student, StudentAttendance $attendance): void
    {
        $phone = $student->parent_phone
            ?? optional($student->parent)->phone
            ?? null;

        if (empty($phone)) {
            return;
        }

        $dateFormatted = Carbon::parse($attendance->date)->translatedFormat('d F Y');
        $statusRaw = is_string($attendance->status) ? $attendance->status : ($attendance->status->value ?? '');
        $statusIndonesian = match ($statusRaw) {
            'present' => 'Hadir',
            'late' => 'Terlambat',
            'permission' => 'Izin',
            'sick' => 'Sakit',
            'absent' => 'Alpha / Tidak Hadir',
            default => 'Tidak Diketahui',
        };

        $message = "SIMPAD Info:\n\nYth. Orang Tua/Wali dari {$student->name},\n\nDiberitahukan bahwa putra/putri Anda tercatat *{$statusIndonesian}* pada tanggal {$dateFormatted}.\n";

        if ($attendance->check_in_time) {
            $message .= "Jam Masuk: {$attendance->check_in_time}\n";
        }
        if ($attendance->note) {
            $message .= "Catatan: {$attendance->note}\n";
        }

        $message .= "\nTerima kasih.\nSistem Presensi Sekolah SIMPAD";

        SendWhatsAppNotification::dispatchAfterResponse($phone, $message);
    }
}
