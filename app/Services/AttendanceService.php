<?php

namespace App\Services;

use App\Models\PresensiSession;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\StudentClass;
use App\Models\Schedule;
use App\Jobs\SendWhatsAppNotification;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AttendanceService
{
    /**
     * Guru/Admin melakukan input presensi kelas manual atau bulk.
     */
    public function recordClassAttendance(?int $teacherId, int $classId, string $date, array $attendances): array
    {
        $schoolId = DB::table('classes')->where('id', $classId)->value('school_id');
        if (!$schoolId) {
            throw new \Exception("Kelas tidak ditemukan.");
        }

        $recordedCount = 0;

        DB::transaction(function () use ($schoolId, $classId, $teacherId, $date, $attendances, &$recordedCount) {
            foreach ($attendances as $att) {
                $studentId = $att['student_id'];
                $status = $att['status']; // present, late, permission, sick, absent
                $note = $att['note'] ?? null;

                // Verifikasi siswa berada di kelas tersebut
                $student = Student::where('id', $studentId)->where('class_id', $classId)->first();
                if (!$student) {
                    continue;
                }

                // Simpan atau update kehadiran
                $attendance = StudentAttendance::updateOrCreate(
                    [
                        'student_id' => $studentId,
                        'date' => $date,
                    ],
                    [
                        'school_id' => $schoolId,
                        'class_id' => $classId,
                        'teacher_id' => $teacherId,
                        'status' => $status,
                        'note' => $note,
                        'check_in_time' => $status === 'present' || $status === 'late' ? ($att['check_in_time'] ?? Carbon::now()->toTimeString()) : null,
                    ]
                );

                $recordedCount++;

                // Trigger notifikasi WhatsApp ke Orang Tua
                $this->triggerWhatsAppNotification($student, $attendance);
            }
        });

        return [
            'success' => true,
            'count' => $recordedCount,
        ];
    }

    /**
     * Siswa melakukan presensi mandiri lewat scan QR Code.
     */
    public function recordSelfPresence(int $studentId, string $qrCode): StudentAttendance
    {
        $student = Student::findOrFail($studentId);

        // Contoh format QR Code: "session_{id}"
        if (!str_starts_with($qrCode, 'session_')) {
            throw new \Exception("Format QR Code tidak valid.");
        }

        $sessionId = (int) str_replace('session_', '', $qrCode);
        $session = PresensiSession::with(['schedule.classHour'])->findOrFail($sessionId);

        if ($session->status !== 'open') {
            throw new \Exception("Sesi presensi ini sedang tidak dibuka.");
        }

        // Cek kecocokan kelas siswa dengan kelas jadwal sesi presensi
        if ($session->schedule && $student->class_id !== $session->schedule->class_id) {
            throw new \Exception("Anda tidak terdaftar di kelas untuk sesi presensi ini.");
        }

        $today = Carbon::today()->toDateString();
        if ($session->date !== $today) {
            throw new \Exception("Sesi presensi ini bukan untuk hari ini.");
        }

        // Cari data presensi siswa hari ini
        $attendance = StudentAttendance::where('student_id', $studentId)
            ->where('date', $today)
            ->first();

        if ($attendance) {
            throw new \Exception("Anda sudah melakukan presensi masuk hari ini.");
        }

        // Presensi Masuk Baru
        $now = Carbon::now();
        $startTime = Carbon::parse($session->start_time ?? $session->schedule->classHour->start_time ?? '07:00:00');
        $diffInMinutes = $startTime->diffInMinutes($now, false);

        $status = 'present';
        if ($diffInMinutes > 15) {
            $status = 'late';
        }

        $attendance = StudentAttendance::create([
            'school_id' => $student->school_id,
            'class_id' => $student->class_id,
            'student_id' => $studentId,
            'teacher_id' => $session->teacher_id,
            'date' => $today,
            'check_in_time' => $now->toTimeString(),
            'status' => $status,
            'note' => $status === 'late' ? 'Terlambat scan QR (' . $diffInMinutes . ' menit)' : 'Scan QR tepat waktu',
        ]);

        // Kirim notifikasi masuk ke orang tua
        $this->triggerWhatsAppNotification($student, $attendance);

        return $attendance;
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
        $student = Student::findOrFail($attendance->student_id);

        if ($verificationStatus === 'rejected') {
            $attendance->status = 'absent';
        }
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
        $phone = null;
        if ($student->parent) {
            $phone = $student->parent->phone;
        }

        if (empty($phone)) {
            return;
        }

        $dateFormatted = Carbon::parse($attendance->date)->translatedFormat('d F Y');
        $statusIndonesian = match ($attendance->status) {
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

        dispatch(new SendWhatsAppNotification($phone, $message));
    }
}
