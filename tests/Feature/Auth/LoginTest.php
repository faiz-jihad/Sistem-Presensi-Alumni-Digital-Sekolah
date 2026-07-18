<?php

use App\Models\User;
use App\Models\School;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

test('google web login callback redirects to login with error for alumni', function () {
    $school = School::create([
        'name' => 'SMK Negeri 1 Jakarta',
        'npsn' => '20234567',
        'address' => 'Jakarta',
        'status' => 'active',
    ]);

    $user = User::create([
        'name' => 'Alumni Budi',
        'email' => 'alumni.budi@gmail.com',
        'role' => 'alumni',
        'school_id' => $school->id,
        'status' => 'active',
        'password' => bcrypt('password'),
    ]);

    Http::fake([
        'https://oauth2.googleapis.com/token' => Http::response([
            'id_token' => 'mocked-id-token',
        ], 200),
        'https://oauth2.googleapis.com/tokeninfo*' => Http::response([
            'email' => 'alumni.budi@gmail.com',
            'sub' => '123456789',
            'picture' => 'http://example.com/avatar.jpg'
        ], 200)
    ]);

    $response = $this->get('/admin/login/google/callback?code=mock-code');

    $response->assertRedirect('/admin/login');
    $response->assertSessionHasErrors(['google' => 'Akun Anda tidak memiliki hak akses ke web panel.']);
});

test('google web login callback logs in superadmin successfully', function () {
    $user = User::create([
        'name' => 'Super Admin',
        'email' => 'superadmin@gmail.com',
        'role' => 'super_admin',
        'school_id' => null,
        'status' => 'active',
        'password' => bcrypt('password'),
    ]);

    Http::fake([
        'https://oauth2.googleapis.com/token' => Http::response([
            'id_token' => 'mocked-id-token',
        ], 200),
        'https://oauth2.googleapis.com/tokeninfo*' => Http::response([
            'email' => 'superadmin@gmail.com',
            'sub' => '123456789',
            'picture' => 'http://example.com/avatar.jpg'
        ], 200)
    ]);

    $response = $this->get('/admin/login/google/callback?code=mock-code');

    $response->assertRedirect('/admin');
    $this->assertAuthenticatedAs($user);
});
