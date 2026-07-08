<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use App\Services\FirebaseNotificationService;

class FcmChannel
{
    protected FirebaseNotificationService $firebase;

    public function __construct(FirebaseNotificationService $firebase)
    {
        $this->firebase = $firebase;
    }

    /**
     * Send the given notification via FCM.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification): void
    {
        // Must implement toFcm method
        if (!method_exists($notification, 'toFcm')) {
            return;
        }

        $message = $notification->toFcm($notifiable);
        
        if (!$message) {
            return;
        }

        $title = $message['title'] ?? 'Notifikasi Baru';
        $body = $message['body'] ?? '';
        $data = $message['data'] ?? [];

        // Check if the notifiable is a User model and has fcmTokens relation
        if (method_exists($notifiable, 'fcmTokens')) {
            try {
                $this->firebase->sendPushNotification($notifiable, $title, $body, $data);
            } catch (\Exception $e) {
                logger()->error('Failed to send FCM push notification via FcmChannel: ' . $e->getMessage());
            }
        }
    }
}
