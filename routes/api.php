<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\SchoolController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\ClassController;
use App\Http\Controllers\Api\TeacherController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // ─── Public routes ──────────────────────────────────────────────────────
    Route::post('/auth/login', [AuthController::class, 'login']);

    // ─── Protected routes (memerlukan token Sanctum) ─────────────────────────
    Route::middleware('auth:sanctum')->group(function () {

        // Auth
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/me', [AuthController::class, 'me']);

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
    });
});