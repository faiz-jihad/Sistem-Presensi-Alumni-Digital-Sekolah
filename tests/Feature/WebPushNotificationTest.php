<?php

use App\Models\User;
use App\Models\School;
use App\Notifications\SiswaPresensiNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

test('user dapat melakukan subscribe web push', function () {
    $school = School::factory()->create(['status' => 'active']);
    $user = User::factory()->create([
        'role' => 'teacher',
        'school_id' => $school->id,
        'status' => 'active',
    ]);

    $response = $this->actingAs($user)
        ->postJson('/webpush/subscribe', [
            'endpoint' => 'https://fcm.googleapis.com/fcm/send/some-token',
            'keys' => [
                'p256dh' => 'BNcRwqHA0VCF5vUz35abcdefg',
                'auth' => '8eUW1g2habcdefg',
            ],
            'content_encoding' => 'aesgcm'
        ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Subscription saved successfully.'
        ]);

    $this->assertDatabaseHas('push_subscriptions', [
        'subscribable_id' => $user->id,
        'endpoint' => 'https://fcm.googleapis.com/fcm/send/some-token',
    ]);
});

test('notifikasi presensi masuk ke database dan menggunakan channel webpush', function () {
    Notification::fake();

    $school = School::factory()->create(['status' => 'active']);
    $user = User::factory()->create([
        'role' => 'teacher',
        'school_id' => $school->id,
        'status' => 'active',
    ]);

    $user->notify(new SiswaPresensiNotification('Test Title', 'Test Body'));

    Notification::assertSentTo(
        $user,
        SiswaPresensiNotification::class,
        function ($notification, $channels) {
            return in_array('database', $channels) && in_array('NotificationChannels\WebPush\WebPushChannel', $channels);
        }
    );
});
