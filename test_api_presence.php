<?php

/**
 * Integration Test Script — Presensi API
 * Run: php test_api_presence.php
 *
 * Strategy: reuse existing data in the DB to avoid FK / NOT NULL issues.
 * All writes are wrapped in a transaction that rolls back at the end.
 */

use App\Models\School;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\StudentClass;
use App\Models\Teacher;
use App\Models\User;
use App\Models\Schedule;
use App\Models\ClassHour;
use App\Models\PresensiSession;
use App\Services\ReportService;
use App\Services\AttendanceService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

// ── Bootstrap Laravel ──────────────────────────────────────────────────────────
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== MEMULAI TEST INTEGRASI PRESENSI API ===\n\n";

\DB::beginTransaction();

try {
    // ── 1. Resolve existing School ─────────────────────────────────────────────
    $school = School::first();
    if (!$school) {
        throw new \Exception("Tidak ada data sekolah di database. Silakan seed data dulu: php artisan db:seed");
    }
    echo "✅ 1. Menggunakan sekolah: [{$school->id}] {$school->name}\n\n";

    // ── 2. Resolve existing Class ──────────────────────────────────────────────
    $class = StudentClass::where('school_id', $school->id)->first();
    if (!$class) {
        throw new \Exception("Tidak ada data kelas untuk sekolah ini. Silakan seed data kelas dulu.");
    }
    echo "✅ 2. Menggunakan kelas: [{$class->id}] {$class->name}\n\n";

    // ── 3. Resolve existing Teacher ────────────────────────────────────────────
    $teacher = Teacher::where('school_id', $school->id)->first();
    if (!$teacher) {
        throw new \Exception("Tidak ada data guru untuk sekolah ini. Silakan seed data guru dulu.");
    }

    // Pastikan guru punya user account
    $teacherUser = $teacher->user_id
        ? User::find($teacher->user_id)
        : User::where('role', 'teacher')->where('school_id', $school->id)->first();

    if (!$teacherUser) {
        // Buat user guru sementara (akan di-rollback)
        $teacherUser = User::create([
            'name'       => $teacher->name,
            'email'      => 'guru.test.' . time() . '@test.local',
            'password'   => Hash::make('password123'),
            'role'       => 'teacher',
            'school_id'  => $school->id,
            'status'     => 'active',
        ]);
        $teacher->update(['user_id' => $teacherUser->id]);
    }
    echo "✅ 3. Menggunakan guru: [{$teacher->id}] {$teacher->name} (User: {$teacherUser->email})\n\n";

    // ── 4. Resolve existing Student ────────────────────────────────────────────
    $student = Student::where('school_id', $school->id)
        ->where('class_id', $class->id)
        ->where('status', 'active')
        ->first();

    if (!$student) {
        throw new \Exception("Tidak ada siswa aktif di kelas [{$class->name}]. Silakan seed data siswa dulu.");
    }
    echo "✅ 4. Menggunakan siswa: [{$student->id}] {$student->name} (NIS: {$student->nis})\n";
    echo "   - Nomor WA Ortu: " . ($student->parent_phone ?? '(tidak ada — notif WA akan dilewati)') . "\n\n";

    // ── 5. Resolve existing ClassHour ──────────────────────────────────────────
    $classHour = ClassHour::where('school_id', $school->id)->first();
    if (!$classHour) {
        throw new \Exception("Tidak ada data jam pelajaran. Silakan seed data class_hours dulu.");
    }
    echo "✅ 5. Menggunakan jam pelajaran: [{$classHour->id}] {$classHour->start_time} - {$classHour->end_time}\n\n";

    // ── 6. Resolve or create Schedule ─────────────────────────────────────────
    $dayName = strtolower(Carbon::now()->format('l'));
    $schedule = Schedule::where('school_id', $school->id)
        ->where('class_id', $class->id)
        ->where('teacher_id', $teacher->id)
        ->where('day', $dayName)
        ->where('is_active', true)
        ->first();

    if (!$schedule) {
        // Coba hari lain (agar ada jadwal tersedia)
        $schedule = Schedule::where('school_id', $school->id)
            ->where('class_id', $class->id)
            ->where('teacher_id', $teacher->id)
            ->where('is_active', true)
            ->first();
    }

    if (!$schedule) {
        echo "⚠️  Tidak ada jadwal aktif untuk kelas/guru ini. Sesi presensi akan dibuat manual.\n\n";
        $session = PresensiSession::create([
            'school_id'  => $school->id,
            'schedule_id' => null,
            'teacher_id'  => $teacher->id,
            'date'        => Carbon::today()->toDateString(),
            'start_time'  => $classHour->start_time,
            'end_time'    => $classHour->end_time,
            'status'      => 'open',
        ]);
    } else {
        echo "✅ 6. Menggunakan jadwal: [{$schedule->id}] hari {$schedule->day}\n\n";

        // ── 7. Generate Sesi dari Jadwal ──────────────────────────────────────
        echo "7. Membuat Sesi Presensi ...\n";
        $session = PresensiSession::firstOrCreate(
            [
                'schedule_id' => $schedule->id,
                'date'        => Carbon::today()->toDateString(),
            ],
            [
                'school_id'  => $school->id,
                'teacher_id'  => $teacher->id,
                'start_time'  => $classHour->start_time,
                'end_time'    => $classHour->end_time,
                'status'      => 'open',
            ]
        );
        $session->update(['status' => 'open']);
    }
    echo "✅ 7. Sesi Presensi ID [{$session->id}] Status [{$session->status}] Token QR: session_{$session->id}\n\n";

    // ── 8. Scan QR Mandiri Siswa ───────────────────────────────────────────────
    echo "8. Simulasi Siswa Scan QR (pukul 07:20 — terlambat >15 mnt) ...\n";
    Carbon::setTestNow(Carbon::today()->setTime(7, 20, 0));

    $attendanceService = app(AttendanceService::class);
    // Method: recordSelfPresence(studentId, qrCode)
    $attendance = $attendanceService->recordSelfPresence(
        $student->id,
        "session_{$session->id}"
    );

    echo "✅ 8. Scan Berhasil!\n";
    echo "   - Status: *{$attendance->status}*\n";
    echo "   - Jam Masuk: {$attendance->check_in_time}\n\n";

    Carbon::setTestNow();

    // ── 9. Input Manual oleh Guru ─────────────────────────────────────────────
    echo "9. Simulasi Guru Input Manual (override ke 'present') ...\n";
    $bulkData = [[
        'student_id'    => $student->id,
        'status'        => 'present',
        'note'          => 'Dikoreksi manual oleh guru',
        'check_in_time' => '07:05:00',
    ]];
    // Method: recordClassAttendance(teacherId, classId, date, attendances)
    $result = $attendanceService->recordClassAttendance(
        $teacher->id,
        $class->id,
        Carbon::today()->toDateString(),
        $bulkData
    );
    $updatedAttendance = StudentAttendance::where('student_id', $student->id)
        ->where('date', Carbon::today()->toDateString())
        ->first();
    echo "✅ 9. Status diperbarui: {$updatedAttendance->status} | Catatan: {$updatedAttendance->note}\n\n";

    // ── 10. Ajukan Izin Sakit (hari besok agar tidak konflik dengan step 8) ────
    echo "10. Simulasi Siswa Ajukan Izin Sakit (untuk hari besok) ...\n";
    $tomorrowDate = Carbon::today()->addDay()->toDateString();
    // Method: applyLeave(studentId, array $data)
    $leaveRecord = $attendanceService->applyLeave(
        $student->id,
        [
            'date'   => $tomorrowDate,
            'status' => 'sick',
            'note'   => 'Demam tinggi dan batuk',
        ]
    );
    echo "✅ 10. Izin dibuat: ID [{$leaveRecord->id}] Status [{$leaveRecord->status}] Verifikasi [{$leaveRecord->verification_status}]\n\n";

    // ── 11. Verifikasi Izin oleh Admin ────────────────────────────────────────
    echo "11. Simulasi Admin Setujui Izin ...\n";
    // Method: verifyLeave(attendanceId, verifierUserId, verificationStatus)
    $verified = $attendanceService->verifyLeave($leaveRecord->id, $teacherUser->id, 'approved');
    echo "✅ 11. Verifikasi: [{$verified->verification_status}] | Status Kehadiran: [{$verified->status}]\n\n";

    // ── 12. Tolak Izin (buat izin baru dulu untuk test penolakan) ─────────────
    echo "12. Simulasi Admin TOLAK izin (status → absent) ...\n";
    $leaveRecord2 = $attendanceService->applyLeave(
        $student->id,
        [
            'date'   => Carbon::today()->addDays(2)->toDateString(),
            'status' => 'sick',
            'note'   => 'Sakit perut',
        ]
    );
    $rejected = $attendanceService->verifyLeave($leaveRecord2->id, $teacherUser->id, 'rejected');
    echo "✅ 12. Ditolak: [{$rejected->verification_status}] | Status Kehadiran: [{$rejected->status}]\n\n";

    // ── 13. Broadcast Rekap Harian ke WA Ortu ────────────────────────────────
    echo "13. Broadcast Rekap Harian ke WhatsApp Ortu ...\n";
    $reportService = app(ReportService::class);
    $dailyCount = $reportService->sendDailyRecapToParents(Carbon::today()->toDateString());
    echo "✅ 13. {$dailyCount} pesan rekap harian dijadwalkan ke antrean WA.\n\n";

    // ── 14. Broadcast Rekap Bulanan ke WA Ortu ───────────────────────────────
    echo "14. Broadcast Rekap Bulanan ke WhatsApp Ortu ...\n";
    $monthlyCount = $reportService->sendMonthlyRecapToParents(
        (int) Carbon::today()->month,
        (int) Carbon::today()->year
    );
    echo "✅ 14. {$monthlyCount} pesan rekap bulanan dijadwalkan ke antrean WA.\n\n";

    echo "===========================================\n";
    echo "✅ SEMUA TEST INTEGRASI BERHASIL DIJALANKAN\n";
    echo "===========================================\n";

} catch (\Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
} finally {
    \DB::rollBack();
    echo "\n🔁 Semua data test di-rollback. Database tetap bersih.\n";
}
