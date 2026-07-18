<?php

use App\Models\User;
use App\Models\School;
use Filament\Panel;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

test('super admin can access panel', function () {
    $user = new User(['role' => 'super_admin']);
    $panel = mock(Panel::class);
    
    expect($user->canAccessPanel($panel))->toBeTrue();
});

test('admin with school can access panel', function () {
    $school = School::create([
        'name' => 'SMK Negeri 1 Jakarta',
        'npsn' => '20234567',
        'address' => 'Jakarta',
        'status' => 'active',
    ]);
    $user = User::create([
        'name' => 'Admin User',
        'email' => 'admin@gmail.com',
        'role' => 'admin',
        'school_id' => $school->id,
        'status' => 'active',
        'password' => bcrypt('password'),
    ]);
    $panel = mock(Panel::class);
    
    expect($user->canAccessPanel($panel))->toBeTrue();
});

test('admin without school cannot access panel', function () {
    $user = User::create([
        'name' => 'Admin No School',
        'email' => 'adminnoschool@gmail.com',
        'role' => 'admin',
        'school_id' => null,
        'status' => 'active',
        'password' => bcrypt('password'),
    ]);
    $panel = mock(Panel::class);
    
    expect($user->canAccessPanel($panel))->toBeFalse();
});

test('teacher with school can access panel', function () {
    $school = School::create([
        'name' => 'SMK Negeri 1 Jakarta',
        'npsn' => '20234567',
        'address' => 'Jakarta',
        'status' => 'active',
    ]);
    $user = User::create([
        'name' => 'Teacher User',
        'email' => 'teacher@gmail.com',
        'role' => 'teacher',
        'school_id' => $school->id,
        'status' => 'active',
        'password' => bcrypt('password'),
    ]);
    $panel = mock(Panel::class);
    
    expect($user->canAccessPanel($panel))->toBeTrue();
});

test('alumni cannot access panel even with school', function () {
    $school = School::create([
        'name' => 'SMK Negeri 1 Jakarta',
        'npsn' => '20234567',
        'address' => 'Jakarta',
        'status' => 'active',
    ]);
    $user = User::create([
        'name' => 'Alumni User',
        'email' => 'alumni@gmail.com',
        'role' => 'alumni',
        'school_id' => $school->id,
        'status' => 'active',
        'password' => bcrypt('password'),
    ]);
    $panel = mock(Panel::class);
    
    expect($user->canAccessPanel($panel))->toBeFalse();
});

test('student cannot access panel even with school', function () {
    $school = School::create([
        'name' => 'SMK Negeri 1 Jakarta',
        'npsn' => '20234567',
        'address' => 'Jakarta',
        'status' => 'active',
    ]);
    $user = User::create([
        'name' => 'Student User',
        'email' => 'student@gmail.com',
        'role' => 'student',
        'school_id' => $school->id,
        'status' => 'active',
        'password' => bcrypt('password'),
    ]);
    $panel = mock(Panel::class);
    
    expect($user->canAccessPanel($panel))->toBeFalse();
});
