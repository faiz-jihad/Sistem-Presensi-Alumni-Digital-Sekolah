<?php

namespace App\Services;

use App\Models\FcmToken;
use App\Models\User;
use Illuminate\Support\Facades\Http;

class FirebaseNotificationService
{
    /**
     * Mendapatkan kredensial Service Account Firebase dari config (.env) atau file storage.
     */
    private function getServiceAccount(): ?array
    {
        // 1. Coba dari environment variable (FIREBASE_SERVICE_ACCOUNT_JSON)
        $configJson = config('services.firebase.service_account_json');
        if ($configJson) {
            $decoded = json_decode($configJson, true);
            if (is_array($decoded) && isset($decoded['private_key'], $decoded['client_email'])) {
                return $decoded;
            }
        }

        // 2. Coba dari berkas file storage
        $serviceAccountPath = storage_path('app/firebase/service-account.json');
        if (file_exists($serviceAccountPath)) {
            $decoded = json_decode(file_get_contents($serviceAccountPath), true);
            if (is_array($decoded) && isset($decoded['private_key'], $decoded['client_email'])) {
                return $decoded;
            }
        }

        return null;
    }

    /**
     * Get FCM v1 OAuth2 Access Token.
     */
    public function getAccessToken(): ?string
    {
        // Cache token selama 58 menit (3480 detik) untuk menghemat limit request Google OAuth2
        return cache()->remember('firebase_fcm_access_token', 3480, function () {
            $serviceAccount = $this->getServiceAccount();

            if (!$serviceAccount) {
                logger()->error('Firebase service account credentials not found in env (FIREBASE_SERVICE_ACCOUNT_JSON) or storage.');
                return null;
            }
            
            $privateKey = $serviceAccount['private_key'];
            $clientEmail = $serviceAccount['client_email'];
            $tokenUri = $serviceAccount['token_uri'] ?? 'https://oauth2.googleapis.com/token';

            $now = time();
            $header = json_encode(['alg' => 'RS256', 'typ' => 'JWT']);
            $claimSet = json_encode([
                'iss' => $clientEmail,
                'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
                'aud' => $tokenUri,
                'exp' => $now + 3600,
                'iat' => $now
            ]);

            $base64UrlHeader = $this->base64UrlEncode($header);
            $base64UrlClaimSet = $this->base64UrlEncode($claimSet);

            $signatureInput = $base64UrlHeader . '.' . $base64UrlClaimSet;
            
            $signature = '';
            if (!openssl_sign($signatureInput, $signature, $privateKey, 'SHA256')) {
                logger()->error('Failed to sign JWT for Firebase OAuth2.');
                return null;
            }

            $base64UrlSignature = $this->base64UrlEncode($signature);
            $jwt = $signatureInput . '.' . $base64UrlSignature;

            $response = Http::asForm()->timeout(15)->retry(2, 250)->post($tokenUri, [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt
            ]);

            if ($response->failed()) {
                logger()->error('Failed to fetch Firebase FCM OAuth2 access token: ' . $response->body());
                return null;
            }

            return $response->json()['access_token'] ?? null;
        });
    }

    /**
     * Send Push Notification to all user device tokens.
     */
    public function sendPushNotification(User $user, string $title, string $body, array $data = []): array
    {
        $tokens = $user->fcmTokens()->pluck('token')->toArray();

        if (empty($tokens)) {
            logger()->warning('FCM push skipped because user has no device token.', [
                'user_id' => $user->id,
                'role' => $user->role,
            ]);

            return ['success' => false, 'message' => 'No active device tokens found for this user.'];
        }

        return $this->sendToTokens($tokens, $title, $body, $data);
    }

    /**
     * Send Push Notification to specific device tokens.
     */
    public function sendToTokens(array $tokens, string $title, string $body, array $data = []): array
    {
        $accessToken = $this->getAccessToken();
        if (!$accessToken) {
            return ['success' => false, 'message' => 'Failed to obtain Firebase access token.'];
        }

        $serviceAccount = $this->getServiceAccount();
        if (!$serviceAccount || empty($serviceAccount['project_id'])) {
            return ['success' => false, 'message' => 'Firebase Project ID is missing.'];
        }
        $projectId = $serviceAccount['project_id'];

        $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

        $successCount = 0;
        $failedCount = 0;

        foreach ($tokens as $token) {
            $payload = [
                'message' => [
                    'token' => $token,
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                    ],
                    'android' => [
                        'priority' => 'high',
                        'notification' => [
                            'channel_id' => 'attendance_notifications_v3',
                            'sound' => 'bell',
                            'default_vibrate_timings' => true,
                        ],
                    ],
                    'apns' => [
                        'headers' => [
                            'apns-priority' => '10',
                        ],
                        'payload' => [
                            'aps' => [
                                'sound' => 'bell.wav',
                                'badge' => 1,
                            ],
                        ],
                    ],
                    'webpush' => [
                        'headers' => [
                            'Urgency' => 'high',
                        ],
                        'notification' => [
                            'icon' => url('/favicon.ico'),
                            'badge' => url('/favicon.ico'),
                            'tag' => isset($data['notification_id'])
                                ? 'simpad-' . $data['notification_id']
                                : 'simpad-notification',
                        ],
                    ],
                ]
            ];

            if (!empty($data)) {
                $stringData = [];
                foreach ($data as $key => $value) {
                    $stringData[(string)$key] = (string)$value;
                }
                $payload['message']['data'] = $stringData;
            }

            $payload['message']['data']['title'] = $title;
            $payload['message']['data']['body'] = $body;

            $response = Http::withToken($accessToken)
                ->timeout(15)
                ->retry(2, 250, throw: false)
                ->post($url, $payload);

            if ($response->successful()) {
                $successCount++;
            } else {
                $failedCount++;
                logger()->warning('FCM Notification failed for token: ' . $token . ' Response: ' . $response->body());
                
                $fcmErrorCode = $response->json('error.details.0.errorCode');

                // Hanya hapus token yang benar-benar sudah tidak terdaftar.
                if ($response->status() === 404 || $fcmErrorCode === 'UNREGISTERED') {
                    FcmToken::where('token', $token)->delete();
                }
            }
        }

        return [
            'success' => $successCount > 0,
            'success_count' => $successCount,
            'failed_count' => $failedCount,
        ];
    }

    /**
     * Base64URL encoding helper.
     */
    private function base64UrlEncode(string $data): string
    {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data));
    }
}
