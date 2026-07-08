<?php

namespace App\Services;

use App\Models\FcmToken;
use App\Models\User;
use Illuminate\Support\Facades\Http;

class FirebaseNotificationService
{
    /**
     * Get FCM v1 OAuth2 Access Token.
     */
    public function getAccessToken(): ?string
    {
        // Cache token to prevent excessive requests (valid for 1 hour, cached for 58 minutes)
        return cache()->remember('firebase_fcm_access_token', 3480, function () {
            $serviceAccountPath = storage_path('app/firebase/service-account.json');

            if (!file_exists($serviceAccountPath)) {
                logger()->error('Firebase service account file not found at ' . $serviceAccountPath);
                return null;
            }

            $serviceAccount = json_decode(file_get_contents($serviceAccountPath), true);
            
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

            $response = Http::asForm()->post($tokenUri, [
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

        $serviceAccountPath = storage_path('app/firebase/service-account.json');
        if (!file_exists($serviceAccountPath)) {
            return ['success' => false, 'message' => 'Firebase service account file not found.'];
        }
        $serviceAccount = json_decode(file_get_contents($serviceAccountPath), true);
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
                ]
            ];

            if (!empty($data)) {
                $stringData = [];
                foreach ($data as $key => $value) {
                    $stringData[(string)$key] = (string)$value;
                }
                $payload['message']['data'] = $stringData;
            }

            $response = Http::withToken($accessToken)->post($url, $payload);

            if ($response->successful()) {
                $successCount++;
            } else {
                $failedCount++;
                logger()->warning('FCM Notification failed for token: ' . $token . ' Response: ' . $response->body());
                
                // If token is invalid or unregistered, clean it up from DB
                if ($response->status() === 400 || $response->status() === 404) {
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
