<?php

use App\Models\School;
use App\Models\User;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Seed roles and permissions if needed
    try {
        $this->artisan('db:seed', ['--class' => 'RoleAndPermissionSeeder']);
    } catch (\Throwable $e) {
        // Fallback if seeder is not present or fails
    }
});

test('login response redirects to http://127.0.0.1:8000/admin/', function () {
    $response = app(LoginResponse::class);
    $request = request();
    
    $redirectResponse = $response->toResponse($request);
    
    expect($redirectResponse->getTargetUrl())->toBe('http://127.0.0.1:8000/admin/');
});
