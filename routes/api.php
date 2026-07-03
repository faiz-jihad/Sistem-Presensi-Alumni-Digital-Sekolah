<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\SchoolController;
use App\Http\Controllers\Api\StudentController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    
    // Public
    Route::post('/auth/login', [AuthController::class, 'login']);

    // Protected
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
        
    });
});