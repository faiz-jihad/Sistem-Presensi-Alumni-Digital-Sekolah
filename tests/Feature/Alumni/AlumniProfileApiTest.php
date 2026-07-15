<?php

use App\Models\Alumni;
use App\Models\AlumniProfile;
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

test('melengkapi profil alumni menyimpan waktu penyelesaian profil', function () {
    $school = School::create([
        'name' => 'SMK Alumni Aktif',
        'npsn' => '12348765',
        'address' => 'Bandung',
        'status' => 'active',
    ]);

    $user = User::create([
        'name' => 'Alumni Baru',
        'email' => 'alumni.baru@example.com',
        'password' => 'password123',
        'role' => 'alumni',
        'school_id' => $school->id,
        'status' => 'active',
    ]);

    $alumni = Alumni::create([
        'school_id' => $school->id,
        'user_id' => $user->id,
        'nisn' => '9988776655',
        'name' => $user->name,
        'gender' => 'female',
        'graduation_year' => 2026,
        'class_name' => 'XII RPL 1',
        'verification_status' => 'verified',
    ]);

    AlumniProfile::create([
        'alumni_id' => $alumni->id,
        'current_status' => 'unemployed',
    ]);

    $this->actingAs($user, 'sanctum')
        ->putJson('/api/v1/alumni/profile', [
            'current_status' => 'working',
            'company_name' => 'SIMPAD Teknologi',
            'job_position' => 'Developer',
            'city' => 'Bandung',
            'province' => 'Jawa Barat',
            'whatsapp' => '081234567890',
        ])
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.profile.current_status', 'working')
        ->assertJsonPath('data.profile.city', 'Bandung')
        ->assertJsonPath('data.profile.whatsapp', '081234567890')
        ->assertJsonPath('data.profile.profile_completed_at', fn ($value) => is_string($value) && $value !== '');

    expect($alumni->profile()->value('profile_completed_at'))->not->toBeNull();
});
