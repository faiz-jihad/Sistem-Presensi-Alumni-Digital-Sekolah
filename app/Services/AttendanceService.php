<?php

namespace App\Services;

use App\Enums\SessionStatus;
use App\Models\PresensiSession;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Jobs\SendWhatsAppNotification;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AttendanceService
{
    /**
     * Guru/Admin melakukan input presensi kelas manual atau bulk.
     */
    public function recordClassAttendance(?int $teacherId, int $classId, string $date, array $attendances, ?int $presensiSessionId = null): array
    {
        if (empty($attendances)) {
            throw new \Exception('Daftar kehadiran kosong.');
        }

        $schoolId = DB::table('classes')->where('id', $classId)->value('school_id');
        if (!$schoolId) {
            throw new \Exception('Kelas tidak ditemukan.');
        }

        if ($presensiSessionId) {
            $sessionExists = PresensiSession::where('id', $presensiSessionId)->exists();
            if (!$sessionExists) {
                throw new \Exception('Sesi presensi tidak ditemukan.');
            }
        } else {
            $presensiSessionId = PresensiSession::where('date', $date)
                ->where(function ($query) use ($classId) {
                    $query->where('class_id', $classId)
                        ->orWhereHas('schedule', function ($scheduleQuery) use ($classId) {
                            $scheduleQuery->where('class_id', $classId);
                        });
                })
                ->latest('id')
                ->value('id');
        }

        $recordedCount = 0;
        $seenStudentIds = [];
        DB::transaction(function () use ($schoolId, $classId, $teacherId, $date, $attendances, $presensiSessionId, &$recordedCount, &$seenStudentIds) {
            foreach ($attendances as $att) {
                $studentId = $att['student_id'] ?? null;
                $status = $att['status'] ?? null;
                $note = $att['note'] ?? null;
                // Prioritas: session_id per-item > session_id global
                $sessionId = $att['presensi_session_id'] ?? $presensiSessionId;

                if (empty($studentId) || !in_array($status, ['present', 'late', 'permission', 'sick', 'absent'], true)) {
                    continue;
                }

                if (in_array($studentId, $seenStudentIds, true)) {
                    continue;
                }

                $seenStudentIds[] = $studentId;

                // Verifikasi siswa berada di kelas tersebut
                $student = Student::where('id', $studentId)->where('class_id', $classId)->first();
                if (!$student) {
                    continue;
                }

                $match = $sessionId
                    ? [
                        'student_id' => $studentId,
                        'presensi_session_id' => $sessionId,
                    ]
                    : [
                        'student_id' => $studentId,
                        'date' => $date,
                    ];

                $existingAttendance = $sessionId
                    ? StudentAttendance::where('student_id', $studentId)
                        ->where(function ($query) use ($sessionId, $date, $classId) {
                            $query->where('presensi_session_id', $sessionId)
                                ->orWhere(function ($fallbackQuery) use ($date, $classId) {
                                    $fallbackQuery->where('date', $date)
                                        ->where('class_id', $classId);
                                });
                        })
                        ->orderByRaw('CASE WHEN presensi_session_id = ? THEN 0 ELSE 1 END', [$sessionId])
                        ->first()
                    : StudentAttendance::where($match)->first();

                $values = [
                    'school_id'           => $schoolId,
                    'class_id'            => $classId,
                    'teacher_id'          => $teacherId,
                    'status'              => $status,
                    'note'                => $note,
                    'date'                => $date,
                ];

                if ($sessionId) {
                    $values['presensi_session_id'] = $sessionId;
                }

                if (in_array($status, ['present', 'late'], true)) {
                    if (array_key_exists('check_in_time', $att)) {
                        $values['check_in_time'] = $att['check_in_time'];
                    } elseif (!$existingAttendance || empty($existingAttendance->check_in_time)) {
                        $values['check_in_time'] = Carbon::now()->toTimeString();
                    }
                } else {
                    $values['check_in_time'] = null;
                }

                if ($existingAttendance) {
                    $existingAttendance->update($values);
                    $attendance = $existingAttendance->refresh();
                } else {
                    $attendance = StudentAttendance::create(array_merge($match, $values));
                }

                $recordedCount++;
            }
        });

        return [
            'success' => true,
            'count'   => $recordedCount,
        ];
    }

    /**
     * Siswa melakukan presensi mandiri lewat scan QR Code.
     */
    public function recordSelfPresence(int $studentId, string $qrCode): StudentAttendance
    {
        $student = Student::findOrFail($studentId);

        // Format QR Code yang valid: "session_{id}"
        if (!str_starts_with($qrCode, 'session_')) {
            throw new \Exception('Format QR Code tidak valid.');
        }

        $sessionId = (int) str_replace('session_', '', $qrCode);

        if ($sessionId <= 0) {
            throw new \Exception('Format QR Code tidak valid.');
        }

        $session = PresensiSession::with(['schedule.classHour'])->findOrFail($sessionId);

        if ($session->status !== SessionStatus::Open) {
            throw new \Exception('Sesi presensi ini sedang tidak dibuka.');
        }

        // Cek kecocokan kelas siswa dengan kelas jadwal sesi presensi
        if ($session->schedule && $student->class_id !== $session->schedule->class_id) {
            throw new \Exception('Anda tidak terdaftar di kelas untuk sesi presensi ini.');
        }

        $today = Carbon::today()->toDateString();
        if ($session->date !== $today) {
            throw new \Exception('Sesi presensi ini bukan untuk hari ini.');
        }

        // Null-safe: ambil end_time dari sesi atau dari classHour
        $endTime  = $session->end_time ?? $session->schedule?->classHour?->end_time;
        $startTime = $session->start_time ?? $session->schedule?->classHour?->start_time;

        $sessionEndTime = $this->resolveSessionDateTime($session->date, $endTime, $startTime);

        if ($sessionEndTime && Carbon::now()->greaterThan($sessionEndTime)) {
            throw new \Exception('Sesi presensi ini sudah berakhir.');
        }

        $now = Carbon::now();

        // Jika sudah ada record presensi hari ini untuk siswa ini:
        // update presensi_session_id supaya terhubung ke sesi ini
        $attendance = StudentAttendance::where('student_id', $studentId)
            ->where('date', $today)
            ->first();

        if ($attendance) {
            throw new \Exception('Anda sudah melakukan presensi', 422);
        }

        // Hitung keterlambatan (null-safe terhadap classHour)
        $startTimeRaw = $session->start_time ?? $session->schedule?->classHour?->start_time ?? '07:00:00';
        $startCarbon  = Carbon::parse($startTimeRaw);
        $diffInMinutes = $startCarbon->diffInMinutes($now, false);

        $status = $diffInMinutes > 15 ? 'late' : 'present';
        $note   = $status === 'late'
            ? 'Terlambat scan QR (' . $diffInMinutes . ' menit)'
            : 'Scan QR tepat waktu';

        // Gunakan updateOrCreate agar aman dari race condition / double submit
        $attendance = StudentAttendance::updateOrCreate(
            [
                'student_id' => $studentId,
                'date'       => $today,
            ],
            [
                'school_id'           => $student->school_id,
                'class_id'            => $student->class_id,
                'teacher_id'          => $session->teacher_id,
                'presensi_session_id' => $session->id,
                'check_in_time'       => $now->toTimeString(),
                'status'              => $status,
                'note'                => $note,
            ]
        );

        // Kirim notifikasi masuk ke orang tua
        $this->triggerWhatsAppNotification($student, $attendance);

        return $attendance;
    }

    private function resolveSessionDateTime(string $date, ?string $time, ?string $startTime = null): ?Carbon
    {
        if (empty($time)) {
            return null;
        }

        $dateTime = Carbon::parse($date . ' ' . $time);

        if (!empty($startTime) && $dateTime->lessThanOrEqualTo(Carbon::parse($date . ' ' . $startTime))) {
            $dateTime->addDay();
        }

        return $dateTime;
    }

    /**
     * Siswa mengajukan izin atau sakit.
     */
    public function applyLeave(int $studentId, array $data): StudentAttendance
    {
        $student = Student::findOrFail($studentId);
        $date = $data['date'] ?? Carbon::today()->toDateString();

        $attendance = StudentAttendance::updateOrCreate(
            [
                'student_id' => $studentId,
                'date' => $date,
            ],
            [
                'school_id' => $student->school_id,
                'class_id' => $student->class_id,
                'status' => $data['status'], // permission atau sick
                'note' => $data['note'] ?? null,
            ]
        );

        return $attendance;
    }

    /**
     * Admin/Wali Kelas memverifikasi pengajuan izin/sakit siswa.
     */
    public function verifyLeave(int $attendanceId, int $verifierUserId, string $verificationStatus): StudentAttendance
    {
        $attendance = StudentAttendance::findOrFail($attendanceId);
        $student    = Student::findOrFail($attendance->student_id);

        // Hanya bisa verifikasi jika status attendance adalah izin atau sakit
        if (!in_array($attendance->status, ['permission', 'sick'], true)) {
            throw new \Exception('Hanya pengajuan izin atau sakit yang dapat diverifikasi.');
        }

        // Update status attendance jika ditolak → alpha
        if ($verificationStatus === 'rejected') {
            $attendance->status = 'absent';
        }

        // Simpan status verifikasi, verifier, dan waktu verifikasi
        $attendance->verification_status = $verificationStatus;
        $attendance->verified_by         = $verifierUserId;
        $attendance->verified_at         = Carbon::now();
        $attendance->save();

        // Kirim update notifikasi WhatsApp ke orang tua
        $this->triggerWhatsAppNotification($student, $attendance);

        return $attendance;
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

