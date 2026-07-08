<?php

use App\Models\School;
use App\Models\User;
use App\Models\FcmToken;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'RoleAndPermissionSeeder']);
});

test('user can register device fcm token', function () {
    $school = School::create([
        'name' => 'SMK Negeri 1 Jakarta',
        'npsn' => '20234567',
        'address' => 'Jakarta',
        'status' => 'active',
    ]);

    $user = User::create([
        'name' => 'Budi FCM',
        'email' => 'budi.fcm@gmail.com',
        'role' => 'student',
        'school_id' => $school->id,
        'status' => 'active',
        'password' => 'password123',
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->postJson('/api/v1/device-token', [
            'token' => 'fcm-token-12345',
            'device_type' => 'android',
        ]);

    $response->assertStatus(200)
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Token perangkat berhasil didaftarkan.');

    $this->assertDatabaseHas('fcm_tokens', [
        'user_id' => $user->id,
        'token' => 'fcm-token-12345',
        'device_type' => 'android',
    ]);
});
