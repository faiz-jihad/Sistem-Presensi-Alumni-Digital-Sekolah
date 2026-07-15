<?php

use App\Models\FcmToken;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('pengguna panel dapat mendaftarkan token FCM web', function () {
    $user = User::factory()->create([
        'role' => 'super_admin',
        'status' => 'active',
    ]);

    $response = $this
        ->actingAs($user)
        ->postJson('/admin/device-token', [
            'token' => 'web-token-for-testing',
        ]);

    $response
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.device_type', 'web');

    expect(FcmToken::query()
        ->where('user_id', $user->id)
        ->where('token', 'web-token-for-testing')
        ->where('device_type', 'web')
        ->exists())->toBeTrue();
});

test('token FCM web membutuhkan sesi login', function () {
    $this->postJson('/admin/device-token', [
        'token' => 'unauthenticated-web-token',
    ])->assertUnauthorized();
});

test('pendaftaran token FCM web mengganti token browser lama milik pengguna', function () {
    $user = User::factory()->create([
        'role' => 'super_admin',
        'status' => 'active',
    ]);

    FcmToken::create([
        'user_id' => $user->id,
        'token' => 'old-web-token',
        'device_type' => 'web',
    ]);

    $this
        ->actingAs($user)
        ->postJson('/admin/device-token', [
            'token' => 'new-web-token',
        ])
        ->assertOk();

    expect(FcmToken::query()
        ->where('user_id', $user->id)
        ->where('device_type', 'web')
        ->pluck('token')
        ->all())->toBe(['new-web-token']);
});

test('script FCM tidak meneruskan toast halaman sebagai notifikasi sistem', function () {
    $script = view('filament.components.firebase-script')->render();

    expect($script)
        ->not->toContain("addEventListener('notificationSent'")
        ->toContain('messaging.onMessage');
});
