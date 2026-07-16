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

test('payload FCM web menyertakan judul dan isi notifikasi desktop', function () {
    $user = User::factory()->create([
        'role' => 'super_admin',
        'status' => 'active',
    ]);

    FcmToken::create([
        'user_id' => $user->id,
        'token' => 'active-web-token',
        'device_type' => 'web',
    ]);

    config()->set('services.firebase.service_account_json', json_encode([
        'private_key' => 'unused-in-test',
        'client_email' => 'firebase@example.test',
        'project_id' => 'simpad-test',
    ]));
    Cache::put('firebase_fcm_access_token', 'test-access-token', now()->addHour());

    Http::fake([
        'https://fcm.googleapis.com/*' => Http::response([
            'name' => 'projects/simpad-test/messages/test-message',
        ]),
    ]);

    $result = app(FirebaseNotificationService::class)->sendPushNotification(
        $user,
        'Presensi berhasil',
        'Ahmad hadir pada pukul 07.15.',
        ['notification_id' => 'notification-123'],
    );

    expect($result['success'])->toBeTrue();

    Http::assertSent(function ($request) {
        $message = $request->data()['message'] ?? [];

        return data_get($message, 'webpush.notification.title') === 'Presensi berhasil'
            && data_get($message, 'webpush.notification.body') === 'Ahmad hadir pada pukul 07.15.'
            && data_get($message, 'webpush.notification.tag') === 'simpad-notification-123'
            && data_get($message, 'android.notification.channel_id') === 'attendance_notifications_v3'
            && data_get($message, 'apns.payload.aps.sound') === 'bell.wav';
    });
});
