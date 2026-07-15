<?php

use App\Models\Alumni;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('alumni dapat membuka profil miliknya tanpa bergantung pada status sekolah', function () {
    $school = School::create([
        'name' => 'SMK Alumni',
        'npsn' => '87654321',
        'address' => 'Jakarta',
        'status' => 'inactive',
    ]);

    $user = User::create([
        'name' => 'Alumni Aktif',
        'email' => 'alumni.profile@example.com',
        'password' => 'password123',
        'role' => 'alumni',
        'school_id' => $school->id,
        'status' => 'active',
    ]);

    Alumni::create([
        'school_id' => $school->id,
        'user_id' => $user->id,
        'nisn' => '1234567890',
        'name' => $user->name,
        'gender' => 'male',
        'graduation_year' => 2026,
        'class_name' => 'XII RPL 1',
        'verification_status' => 'verified',
    ]);

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/v1/alumni/profile')
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.user.email', $user->email);
});
