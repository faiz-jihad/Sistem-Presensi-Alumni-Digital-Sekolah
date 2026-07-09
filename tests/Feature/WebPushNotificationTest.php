<?php
 
use App\Models\User;
use App\Models\School;
use App\Notifications\SiswaPresensiNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Seed roles & permissions
    $this->artisan('db:seed', ['--class' => 'RoleAndPermissionSeeder']);
});

test('user dapat melakukan subscribe web push', function () {
    $school = School::create([
        'name' => 'SMK Negeri Test',
        'npsn' => '12345678',
        'status' => 'active',
    ]);
    
    $user = User::create([
        'name' => 'Guru Test',
        'email' => 'gurutest@test.com',
        'password' => Hash::make('password'),
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

    $school = School::create([
        'name' => 'SMK Negeri Test',
        'npsn' => '12345678',
        'status' => 'active',
    ]);
    
    $user = User::create([
        'name' => 'Guru Test',
        'email' => 'gurutest@test.com',
        'password' => Hash::make('password'),
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

test('database notification created observer triggers web push for both laravel and filament notifications', function () {
    Notification::fake();

    $school = School::create([
        'name' => 'SMK Negeri Test',
        'npsn' => '12345678',
        'status' => 'active',
    ]);
    
    $user = User::create([
        'name' => 'Guru Test',
        'email' => 'gurutest@test.com',
        'password' => Hash::make('password'),
        'role' => 'teacher',
        'school_id' => $school->id,
        'status' => 'active',
    ]);

    // 1. Uji Laravel standard Database Notification
    $dbNotif = new \Illuminate\Notifications\DatabaseNotification();
    $dbNotif->id = \Illuminate\Support\Str::uuid();
    $dbNotif->type = 'App\Notifications\SomeNotification';
    $dbNotif->data = ['title' => 'Laravel Notif', 'body' => 'Laravel Body'];
    $dbNotif->notifiable()->associate($user);
    $dbNotif->save();

    Notification::assertSentTo(
        $user,
        \App\Notifications\FilamentWebPushNotification::class,
        function ($notification) {
            return $notification->toWebPush(new \stdClass(), null)->toArray()['title'] === 'Laravel Notif';
        }
    );

    // 2. Uji Filament Database Notification
    if (class_exists(\Filament\Notifications\Models\DatabaseNotification::class)) {
        Notification::fake(); // Reset fake assertion counts
        
        $filamentNotif = new \Filament\Notifications\Models\DatabaseNotification();
        $filamentNotif->id = \Illuminate\Support\Str::uuid();
        $filamentNotif->type = \Filament\Notifications\Notification::class;
        $filamentNotif->data = ['title' => 'Filament Notif', 'body' => 'Filament Body'];
        $filamentNotif->notifiable()->associate($user);
        $filamentNotif->save();

        Notification::assertSentTo(
            $user,
            \App\Notifications\FilamentWebPushNotification::class,
            function ($notification) {
                return $notification->toWebPush(new \stdClass(), null)->toArray()['title'] === 'Filament Notif';
            }
        );
    }
});
