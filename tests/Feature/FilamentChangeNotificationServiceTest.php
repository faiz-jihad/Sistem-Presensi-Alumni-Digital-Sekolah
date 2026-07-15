<?php

use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('notifikasi perubahan data langsung tersimpan tanpa menunggu queue worker', function () {
    $school = School::create([
        'name' => 'SMK Notifikasi',
        'npsn' => '87654321',
        'status' => 'active',
    ]);

    $admin = User::factory()->create([
        'role' => 'admin',
        'status' => 'active',
        'school_id' => $school->id,
    ]);

    $this->actingAs($admin);

    $school->update(['name' => 'SMK Notifikasi Diperbarui']);

    $this->assertDatabaseHas('notifications', [
        'notifiable_type' => User::class,
        'notifiable_id' => $admin->id,
    ]);

    $notification = $admin->notifications()
        ->where('data->title', 'Sekolah diperbarui')
        ->first();

    expect($notification)->not->toBeNull()
        ->and($notification->data['title'] ?? null)->toBe('Sekolah diperbarui')
        ->and(DB::table('jobs')->count())->toBe(0);
});

test('perubahan token login user tidak membuat notifikasi', function () {
    $admin = User::factory()->create([
        'role' => 'super_admin',
        'status' => 'active',
    ]);

    $this->actingAs($admin);

    $admin->forceFill(['remember_token' => 'token-login-baru'])->save();

    expect($admin->notifications()->count())->toBe(0)
        ->and(DB::table('jobs')->count())->toBe(0);
});

test('perubahan data bisnis user tetap membuat notifikasi', function () {
    $admin = User::factory()->create([
        'name' => 'Admin Lama',
        'role' => 'super_admin',
        'status' => 'active',
    ]);

    $this->actingAs($admin);

    $admin->update(['name' => 'Admin Baru']);

    $notification = $admin->notifications()
        ->where('data->title', 'User diperbarui')
        ->first();

    expect($notification)->not->toBeNull()
        ->and($notification->data['body'] ?? null)->toContain('Admin Baru diperbarui');
});
