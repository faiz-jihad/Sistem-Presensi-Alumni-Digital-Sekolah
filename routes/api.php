<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\SchoolController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\ClassController;
use App\Http\Controllers\Api\TeacherController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    
    // Route publik
    Route::post('/auth/login', [AuthController::class, 'login']);

    // Route yang memerlukan autentikasi
    Route::middleware('auth:sanctum')->group(function () {
        
        // Auth
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/me', [AuthController::class, 'me']);

        // Role & Permission
        Route::apiResource('roles', RoleController::class);
        Route::apiResource('permissions', PermissionController::class)->only(['index', 'store', 'update', 'destroy']);

        // Master Data
        Route::apiResource('schools', SchoolController::class);
        Route::apiResource('students', StudentController::class);
        
        // Kelas
        Route::get('/classes', [ClassController::class, 'index']);
        Route::get('/classes/{id}', [ClassController::class, 'show']);
        Route::get('/classes/{id}/students', [ClassController::class, 'students']);

        // Guru
        Route::get('/teachers', [TeacherController::class, 'index']);
        Route::get('/teachers/{id}', [TeacherController::class, 'show']);
        Route::get('/teachers/{id}/classes', [TeacherController::class, 'classes']);
        
    });
});