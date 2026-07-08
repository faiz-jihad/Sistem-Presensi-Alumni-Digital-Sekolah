<?php

use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'RoleAndPermissionSeeder']);
});

test('google login returns Sanctum token if user exists and is active', function () {
    $school = School::create([
        'name' => 'SMK Negeri 1 Jakarta',
        'npsn' => '20234567',
        'address' => 'Jakarta',
        'status' => 'active',
    ]);

    $user = User::create([
        'name' => 'Budi Google',
        'email' => 'budi.google@gmail.com',
        'role' => 'student',
        'school_id' => $school->id,
        'status' => 'active',
        'password' => 'password123',
    ]);

    Http::fake([
        'https://oauth2.googleapis.com/tokeninfo*' => Http::response([
            'email' => 'budi.google@gmail.com',
            'sub' => '12345678901234567890',
            'name' => 'Budi Google',
        ], 200)
    ]);

    $response = $this->postJson('/api/v1/auth/google', [
        'id_token' => 'mocked-valid-google-id-token',
    ]);

    $response->assertStatus(200)
        ->assertJsonPath('success', true)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'token',
                'user' => [
                    'id',
                    'name',
                    'email',
                    'role',
                    'school_id',
                ]
            ]
        ]);

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'google_id' => '12345678901234567890',
    ]);
});

test('google login fails if token is invalid', function () {
    Http::fake([
        'https://oauth2.googleapis.com/tokeninfo*' => Http::response([
            'error' => 'invalid_token',
        ], 400)
    ]);

    $response = $this->postJson('/api/v1/auth/google', [
        'id_token' => 'invalid-token',
    ]);

    $response->assertStatus(400)
        ->assertJsonPath('success', false)
        ->assertJsonPath('message', 'Token Google tidak valid atau telah kedaluwarsa.');
});

test('google login fails if user is not registered', function () {
    Http::fake([
        'https://oauth2.googleapis.com/tokeninfo*' => Http::response([
            'email' => 'unregistered@gmail.com',
            'sub' => '99999999999999999999',
            'name' => 'Unknown User',
        ], 200)
    ]);

    $response = $this->postJson('/api/v1/auth/google', [
        'id_token' => 'valid-but-unregistered-token',
    ]);

    $response->assertStatus(404)
        ->assertJsonPath('success', false)
        ->assertJsonPath('message', 'Email Google Anda (unregistered@gmail.com) tidak terdaftar di sistem. Silakan hubungi Admin Sekolah.');
});
