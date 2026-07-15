<?php

use App\Models\FcmToken;
use App\Models\User;
use App\Services\FirebaseNotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

test('token FCM yang sudah tidak terdaftar otomatis dihapus', function () {
    $user = User::factory()->create([
        'role' => 'super_admin',
        'status' => 'active',
    ]);

    FcmToken::create([
        'user_id' => $user->id,
        'token' => 'stale-web-token',
        'device_type' => 'web',
    ]);

    Cache::put('firebase_fcm_access_token', 'test-access-token', now()->addHour());

    Http::fake([
        'https://fcm.googleapis.com/*' => Http::response([
            'error' => [
                'code' => 404,
                'status' => 'NOT_FOUND',
                'details' => [
                    ['errorCode' => 'UNREGISTERED'],
                ],
            ],
        ], 404),
    ]);

    $result = app(FirebaseNotificationService::class)
        ->sendPushNotification($user, 'Tes', 'Tes notifikasi web');

    expect($result['success'])->toBeFalse()
        ->and($result['failed_count'])->toBe(1)
        ->and(FcmToken::where('token', 'stale-web-token')->exists())->toBeFalse();
});
