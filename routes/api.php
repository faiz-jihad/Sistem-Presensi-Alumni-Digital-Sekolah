<?php

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
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // ─── Public routes ──────────────────────────────────────────────────────
    Route::post('/auth/login', [AuthController::class, 'login']);

    // ─── Protected routes (memerlukan token Sanctum) ─────────────────────────
    Route::middleware('auth:sanctum')->group(function () {

        // Alumni profile
        Route::middleware('role:alumni')->group(function () {
            Route::get('/alumni/me', [AuthController::class, 'me']);
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
        // Admin & super_admin: full CRUD
        Route::middleware('role:admin,super_admin')->group(function () {
            Route::apiResource('students', StudentController::class);
        });

        // ─── Data Kelas ─────────────────────────────────────────────────────
        // Admin + guru bisa lihat; hanya admin yang bisa detail siswa
        Route::get('/classes', [ClassController::class, 'index'])
            ->middleware('role:admin,super_admin,teacher');
        Route::get('/classes/{id}', [ClassController::class, 'show'])
            ->middleware('role:admin,super_admin,teacher');
        Route::get('/classes/{id}/students', [ClassController::class, 'students'])
            ->middleware('role:admin,super_admin,teacher');

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
            
            // Input presensi & bulk input (Guru/Admin)
            Route::post('/', [StudentAttendanceController::class, 'store'])
                ->middleware('role:teacher,admin,super_admin');
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

            // Rekap Laporan
            Route::get('/report/daily', [ReportController::class, 'daily'])
                ->middleware('role:admin,super_admin,teacher');
            Route::get('/report/monthly', [ReportController::class, 'monthly'])
                ->middleware('role:admin,super_admin,teacher');
            Route::post('/report/send-daily', [ReportController::class, 'sendDailyRecap'])
                ->middleware('role:admin,super_admin,teacher');
            Route::post('/report/send-monthly', [ReportController::class, 'sendMonthlyRecap'])
                ->middleware('role:admin,super_admin');
        });

        // ─── Sesi Presensi (PresensiSession) ─────────────────────────────────────
        Route::prefix('presensi-sessions')->middleware('role:admin,super_admin,teacher')->group(function () {
            Route::get('/', [PresensiSessionController::class, 'index']);
            Route::post('/', [PresensiSessionController::class, 'store']);
            Route::post('/{id}/close', [PresensiSessionController::class, 'close']);
            Route::get('/{id}/qr', [PresensiSessionController::class, 'showQr']);
        });
    });
});