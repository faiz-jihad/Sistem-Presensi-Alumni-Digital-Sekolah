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
<<<<<<< Updated upstream
use App\Http\Controllers\Api\AlumniController;
use App\Http\Controllers\Api\AlumniProfileController;
use App\Http\Controllers\Api\ExportController;
use App\Http\Controllers\Api\AlumniEventController;
=======
use App\Http\Controllers\Api\TeacherAttendanceController;
use App\Http\Controllers\Api\AlumniController;
use App\Http\Controllers\Api\AlumniProfileController;
use App\Http\Controllers\Api\ExportController;
>>>>>>> Stashed changes
use Illuminate\Support\Facades\Route;

// ─── Public routes ──────────────────────────────────────────────────────
Route::post('/login', [AuthController::class, 'login']);
Route::post('/alumni/register', [AlumniController::class, 'register']);

<<<<<<< Updated upstream
// ─── Protected routes (memerlukan token Sanctum) ─────────────────────────
Route::middleware('auth:sanctum')->group(function () {
=======
    // Public auth and registration
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/auth/login', [AuthController::class, 'login']); // legacy alias
    Route::post('/alumni/register', [AlumniController::class, 'register']);
>>>>>>> Stashed changes

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

<<<<<<< Updated upstream
    // Alumni Profile
    Route::middleware('role:alumni')->group(function () {
        Route::get('/alumni/profile', [AlumniProfileController::class, 'show']);
        Route::put('/alumni/profile', [AlumniProfileController::class, 'update']);
    });
=======
        // Auth management
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);

        // Alumni profile
        Route::middleware('role:alumni')->group(function () {
            Route::get('/alumni/me', [AuthController::class, 'me']); // legacy alias
            Route::get('/alumni/profile', [AlumniProfileController::class, 'show']);
            Route::put('/alumni/profile', [AlumniProfileController::class, 'update']);
        });
>>>>>>> Stashed changes

    // Dashboard — semua role yang sudah login bisa akses
    Route::get('/dashboard', [DashboardController::class, 'index']);

<<<<<<< Updated upstream
    // Dashboard stats — hanya admin & super_admin
    Route::get('/dashboard/stats', [DashboardController::class, 'stats'])
        ->middleware('role:admin,super_admin');
=======
        // ─── Teacher: Jadwal Hari Ini ─────────────────────────────────────
        Route::middleware('role:teacher')->group(function () {
            Route::get('/teacher/today', [AttendanceController::class, 'today']);
        });

        // ─── Teacher Attendance (One-click check-in/out lama) ────────────
        Route::middleware('role:teacher')->prefix('teacher/attendance')->group(function () {
            Route::get('/today', [TeacherAttendanceController::class, 'today']);
            Route::post('/check-in', [TeacherAttendanceController::class, 'checkIn']);
            Route::post('/check-out', [TeacherAttendanceController::class, 'checkOut']);
        });

        // Dashboard stats — hanya admin & super_admin
        Route::get('/dashboard/stats', [DashboardController::class, 'stats'])
            ->middleware('role:admin,super_admin');
>>>>>>> Stashed changes

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

<<<<<<< Updated upstream
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
=======
        // ─── Data Siswa ─────────────────────────────────────────────────────
        Route::middleware('role:admin,super_admin')->group(function () {
            Route::apiResource('students', StudentController::class);
        });

        // ─── Data Kelas ─────────────────────────────────────────────────────
        Route::get('/classes', [ClassController::class, 'index'])
            ->middleware('role:admin,super_admin,teacher');
        Route::get('/classes/{id}', [ClassController::class, 'show'])
            ->middleware('role:admin,super_admin,teacher');
        Route::get('/classes/{id}/students', [ClassController::class, 'students'])
            ->middleware('role:admin,super_admin,teacher');
>>>>>>> Stashed changes

    // ─── Data Guru ───────────────────────────────────────────────────────
    Route::get('/teachers', [TeacherController::class, 'index'])
        ->middleware('role:admin,super_admin');
    Route::get('/teachers/{id}', [TeacherController::class, 'show'])
        ->middleware('role:admin,super_admin,teacher');
    Route::get('/teachers/{id}/classes', [TeacherController::class, 'classes'])
        ->middleware('role:admin,super_admin,teacher');

<<<<<<< Updated upstream
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
=======
        // ─── Data Presensi Siswa (legacy & PRD) ────────────────────────────────
        Route::prefix('attendances')->group(function () {
            Route::get('/', [StudentAttendanceController::class, 'index']);
            Route::post('/', [StudentAttendanceController::class, 'store'])
                ->middleware('role:teacher,admin,super_admin');
            Route::post('/bulk', [StudentAttendanceController::class, 'bulkStore'])
                ->middleware('role:teacher,admin,super_admin');
            Route::post('/presensi', [StudentAttendanceController::class, 'presensiMandiri'])
                ->middleware('role:student');
            Route::post('/izin', [StudentAttendanceController::class, 'storeIzin'])
                ->middleware('role:student');
            Route::post('/{id}/verify', [StudentAttendanceController::class, 'verifyIzin'])
                ->middleware('role:admin,super_admin,teacher');

            Route::get('/report/daily', [ReportController::class, 'daily'])
                ->middleware('role:admin,super_admin,teacher');
            Route::get('/report/monthly', [ReportController::class, 'monthly'])
                ->middleware('role:admin,super_admin,teacher');
            Route::post('/report/send-daily', [ReportController::class, 'sendDailyRecap'])
                ->middleware('role:admin,super_admin,teacher');
            Route::post('/report/send-monthly', [ReportController::class, 'sendMonthlyRecap'])
                ->middleware('role:admin,super_admin');
        });

        // PRD Attendance submit & rekap
        Route::post('/attendance/submit', [StudentAttendanceController::class, 'store'])
            ->middleware('role:teacher,admin,super_admin');
        Route::get('/attendance/daily', [ReportController::class, 'daily'])
            ->middleware('role:admin,super_admin,teacher');
        Route::get('/attendance/monthly', [ReportController::class, 'monthly'])
            ->middleware('role:admin,super_admin,teacher');

        // ─── Modul Presensi Baru (sesuai presensi.md) ───────────────────────
        Route::middleware('role:teacher,admin,super_admin')->group(function () {
            Route::post('/attendance/open', [AttendanceController::class, 'open']);
            Route::post('/attendance/manual', [AttendanceController::class, 'manual']);
            Route::post('/attendance/generate-qr', [AttendanceController::class, 'generateQr']);
            Route::post('/attendance/close', [AttendanceController::class, 'close']);
            Route::get('/attendance/session/{id}', [AttendanceController::class, 'session']);
            Route::get('/attendance/history', [AttendanceController::class, 'history']);
        });

        // Scan QR (siswa)
        Route::middleware('role:student')->group(function () {
            Route::post('/attendance/scan', [AttendanceController::class, 'scan']);
        });

        // ─── Sesi Presensi CRUD (Filament/admin use) ─────────────────────────
        Route::prefix('presensi-sessions')->middleware('role:admin,super_admin,teacher')->group(function () {
            Route::get('/', [PresensiSessionController::class, 'index']);
            Route::post('/', [PresensiSessionController::class, 'store']);
            Route::get('/{id}', [PresensiSessionController::class, 'show']);
            Route::put('/{id}', [PresensiSessionController::class, 'update']);
            Route::patch('/{id}', [PresensiSessionController::class, 'update']);
            Route::delete('/{id}', [PresensiSessionController::class, 'destroy']);
            Route::post('/{id}/open', [PresensiSessionController::class, 'open']);
            Route::post('/{id}/close', [PresensiSessionController::class, 'close']);
            Route::get('/{id}/qr', [PresensiSessionController::class, 'showQr']);
        });

        // PRD Admin Alumni
        Route::get('/admin/alumni/tracer-study', [AlumniController::class, 'tracerStudy'])
            ->middleware('role:admin,super_admin');

        // PRD Exports
        Route::get('/export/attendance', [ExportController::class, 'exportAttendance'])
            ->middleware('role:admin,super_admin,teacher');
        Route::get('/export/alumni', [ExportController::class, 'exportAlumni'])
>>>>>>> Stashed changes
            ->middleware('role:admin,super_admin');
    });
});