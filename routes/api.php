<?php

use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\SchoolController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\ClassController;
use App\Http\Controllers\Api\TeacherController;
use App\Http\Controllers\Api\StudentAttendanceController;
use App\Http\Controllers\Api\PresensiSessionController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\AlumniController;
use App\Http\Controllers\Api\AlumniProfileController;
use App\Http\Controllers\Api\ExportController;
use App\Http\Controllers\Api\AlumniEventController;
use App\Http\Controllers\Api\AlumniJobController;
use App\Http\Controllers\Api\TeacherAttendanceController;
use App\Http\Controllers\Api\AlumniVerificationController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

// ─── Public routes ──────────────────────────────────────────────────────
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/alumni/register', [AlumniController::class, 'register']);

// ─── Protected routes (memerlukan token Sanctum) ─────────────────────────
Route::middleware('auth:sanctum')->group(function () {

        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);

        // Alumni Profile
        Route::middleware('role:alumni')->group(function () {
            Route::get('/alumni/profile', [AlumniProfileController::class, 'show']);
            Route::put('/alumni/profile', [AlumniProfileController::class, 'update']);
            
            // Alumni Jobs
            Route::get('/alumni/jobs', [AlumniJobController::class, 'index']);
        });

        // Dashboard — semua role yang sudah login bisa akses
        Route::get('/dashboard', [DashboardController::class, 'index']);

    // Dashboard stats — hanya admin & super_admin
    Route::get('/dashboard/stats', [DashboardController::class, 'stats'])
        ->middleware('role:admin,super_admin');

        // ─── Role & Permission (hanya super_admin) ─────────────────────────
        Route::middleware('role:super_admin')->group(function () {
            Route::apiResource('roles', RoleController::class);
            Route::apiResource('permissions', PermissionController::class)
                ->only(['index', 'store', 'update', 'destroy']);
        });

        // ─── Master Data: Schools (admin & super_admin) ─────────────────────
        Route::middleware('role:admin,super_admin')->group(function () {
            Route::apiResource('schools', SchoolController::class);
        });

    // ─── Data Siswa ─────────────────────────────────────────────────────
    Route::middleware('role:admin,super_admin')->group(function () {
        Route::apiResource('students', StudentController::class);
    });

    // ─── Data Kelas ─────────────────────────────────────────────────────
    Route::middleware('role:admin,super_admin,teacher')->group(function () {
        Route::get('/classes', [ClassController::class, 'index']);
        Route::get('/classes/{id}', [ClassController::class, 'show']);
        Route::get('/classes/{id}/students', [ClassController::class, 'students']);
    });

        // ─── Data Guru ───────────────────────────────────────────────────────
        Route::get('/teachers', [TeacherController::class, 'index'])
            ->middleware('role:admin,super_admin');
        Route::get('/teachers/{id}', [TeacherController::class, 'show'])
            ->middleware('role:admin,super_admin,teacher');
        Route::get('/teachers/{id}/classes', [TeacherController::class, 'classes'])
            ->middleware('role:admin,super_admin,teacher');

    // ─── Data Presensi / Kehadiran ───────────────────────────────────────────
    Route::prefix('attendances')->group(function () {
        // List presensi
        Route::get('/', [StudentAttendanceController::class, 'index']);
        
        // Bulk input (Guru/Admin)
        Route::post('/bulk', [StudentAttendanceController::class, 'bulkStore'])
            ->middleware('role:teacher,admin,super_admin');
            
        // Presensi mandiri (Siswa) via QR Code
        Route::post('/presensi', [StudentAttendanceController::class, 'presensiMandiri'])
            ->middleware('role:student');
            
        // Ajukan izin/sakit (Siswa)
        Route::post('/izin', [StudentAttendanceController::class, 'storeIzin'])
            ->middleware('role:student');
            
        // Verifikasi izin (Admin & Guru/Wali Kelas)
        Route::post('/{id}/verify', [StudentAttendanceController::class, 'verifyIzin'])
            ->middleware('role:admin,super_admin,teacher');

            Route::post('/report/send-daily', [ReportController::class, 'sendDailyRecap'])
                ->middleware('role:admin,super_admin,teacher');
            Route::post('/report/send-monthly', [ReportController::class, 'sendMonthlyRecap'])
                ->middleware('role:admin,super_admin');
        });

        // Map Specific Attendance Routes from Screenshot
        Route::post('/attendance/submit', [StudentAttendanceController::class, 'store'])
            ->middleware('role:teacher,admin,super_admin');
        Route::get('/attendance/daily', [ReportController::class, 'daily'])
            ->middleware('role:admin,super_admin,teacher');
        Route::get('/attendance/monthly', [ReportController::class, 'monthly'])
            ->middleware('role:admin,super_admin,teacher');

        // ─── Teacher & Attendance Flow Routes (presensi.md) ──────────────────────
        Route::get('/teacher/today', [AttendanceController::class, 'today'])
            ->middleware('role:teacher,admin,super_admin');
        Route::post('/teacher/check-in', [TeacherAttendanceController::class, 'checkIn'])
            ->middleware('role:teacher');
        Route::post('/teacher/check-out', [TeacherAttendanceController::class, 'checkOut'])
            ->middleware('role:teacher');
        Route::get('/teacher-attendance/today', [TeacherAttendanceController::class, 'today'])
            ->middleware('role:teacher');

        Route::post('/attendance/open', [AttendanceController::class, 'open'])
            ->middleware('role:teacher,admin,super_admin');
        Route::post('/attendance/manual', [AttendanceController::class, 'manual'])
            ->middleware('role:teacher,admin,super_admin');
        Route::post('/attendance/generate-qr', [AttendanceController::class, 'generateQr'])
            ->middleware('role:teacher,admin,super_admin');
        Route::post('/attendance/scan', [AttendanceController::class, 'scan'])
            ->middleware('role:student');
        Route::post('/attendance/close', [AttendanceController::class, 'close'])
            ->middleware('role:teacher,admin,super_admin');
        Route::get('/attendance/session/{id}', [AttendanceController::class, 'session'])
            ->middleware('role:teacher,admin,super_admin');
        Route::get('/attendance/history', [AttendanceController::class, 'history'])
            ->middleware('role:teacher,admin,super_admin');

        // ─── Sesi Presensi (PresensiSession) ─────────────────────────────────────
        Route::prefix('presensi-sessions')->middleware('role:admin,super_admin,teacher')->group(function () {
            Route::get('/', [PresensiSessionController::class, 'index']);
            Route::post('/', [PresensiSessionController::class, 'store']);
            Route::post('/{id}/close', [PresensiSessionController::class, 'close']);
            Route::get('/{id}/qr', [PresensiSessionController::class, 'showQr']);
        });

        // ─── Tracer Study ────────────────────────────────────────────────────────
        Route::get('/admin/alumni/tracer-study', [AlumniController::class, 'tracerStudy'])
            ->middleware('role:admin,super_admin');

        // ─── Export Laporan Excel ────────────────────────────────────────────────
        Route::get('/export/attendance', [ExportController::class, 'attendance'])
            ->middleware('role:admin,super_admin,teacher');
        Route::get('/export/alumni', [ExportController::class, 'alumni'])
            ->middleware('role:admin,super_admin');

    // ─── Event Alumni (AlumniEvent) ──────────────────────────────────────────
    Route::prefix('alumni-events')->group(function () {
        Route::get('/', [AlumniEventController::class, 'index']);
        Route::get('/{id}', [AlumniEventController::class, 'show']);
        Route::post('/', [AlumniEventController::class, 'store']);
        Route::post('/{id}', [AlumniEventController::class, 'update']);
        Route::delete('/{id}', [AlumniEventController::class, 'destroy']);
        
        Route::post('/{id}/approve', [AlumniEventController::class, 'approve'])
            ->middleware('role:admin,super_admin');
        Route::post('/{id}/reject', [AlumniEventController::class, 'reject'])
            ->middleware('role:admin,super_admin');
    });

        // ─── Verifikasi Alumni (admin & super_admin) ─────────────────────────────
        Route::prefix('alumni/verification')->middleware('role:admin,super_admin')->group(function () {
            // Daftar alumni menunggu verifikasi (pending)
            Route::get('/pending', [AlumniVerificationController::class, 'index']);

            // Daftar semua alumni (filter: ?status=pending|verified|rejected)
            Route::get('/', [AlumniVerificationController::class, 'list']);

            // Detail satu alumni
            Route::get('/{id}', [AlumniVerificationController::class, 'show']);

            // Statistik ringkasan verifikasi
            Route::get('/stats/summary', [AlumniVerificationController::class, 'stats']);

            // Setujui (verifikasi) alumni
            Route::post('/{id}/approve', [AlumniVerificationController::class, 'approve']);

            // Tolak alumni (dengan alasan opsional di body: { "reason": "..." })
            Route::post('/{id}/reject', [AlumniVerificationController::class, 'reject']);

            // Reset alumni yang ditolak kembali ke pending
            Route::post('/{id}/reset', [AlumniVerificationController::class, 'resetToPending']);
        });
});
});


