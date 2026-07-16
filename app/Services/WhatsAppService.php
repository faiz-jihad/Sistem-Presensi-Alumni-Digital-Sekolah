<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    private string $apiUrl;
    private ?string $apiToken;

    public function __construct()
    {
        $this->apiUrl = config('services.whatsapp.api_url', env('WHATSAPP_API_URL', 'http://localhost:5000/send'));
        $this->apiToken = config('services.whatsapp.api_token', env('WHATSAPP_API_TOKEN'));
    }

    /**
     * Kirim pesan WhatsApp
     *
     * @param string $to Nomor tujuan (format international, misal: 628123456789)
     * @param string $message Isi pesan
     * @return bool
     */
    public function sendMessage(string $to, string $message): bool
    {
        if (empty(trim($to))) {
            Log::warning('WhatsApp recipient is empty.');
            return false;
        }

        $to = $this->formatPhoneNumber($to);

        if (empty($to)) {
            Log::warning('WhatsApp recipient became empty after formatting.');
            return false;
        }

        try {
            $request = Http::timeout(10)->acceptJson();

            if (!empty($this->apiToken)) {
                $request = $request->withHeaders([
                    'Authorization' => $this->apiToken,
                ]);
            }

            // Gunakan format local gateway ('number' bukan 'target')
            $response = $request->post($this->apiUrl, [
                'number' => $to,
                'message' => $message,
            ]);

            if ($response->successful()) {
                $responseData = $response->json();
                if (isset($responseData['status']) && $responseData['status'] === false) {
                    Log::error("WhatsApp local gateway failed to {$to}: " . ($responseData['message'] ?? $response->body()));
                    return false;
                }
                Log::info("WhatsApp sent successfully to {$to} via local gateway.");
                return true;
            }

            Log::error("WhatsApp local gateway error to {$to}. Status: " . $response->status() . " Body: " . $response->body());
            return false;
        } catch (\Exception $e) {
            Log::error("WhatsApp exception to {$to}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Cek status gateway
     */
    public function getGatewayStatus(): array
    {
        $statusUrl = preg_replace('#/send$#', '/status', $this->apiUrl) ?: $this->apiUrl;

        try {
            $response = Http::timeout(3)->acceptJson()->get($statusUrl);
            if ($response->successful()) {
                $data = $response->json();
                return [
                    'ready' => (bool) ($data['whatsapp_ready'] ?? false),
                    'qr' => $data['qr'] ?? null,
                    'user' => $data['user'] ?? null,
                    'error' => null
                ];
            }
        } catch (\Throwable $exception) {
            // Abaikan error dan asumsikan offline
        }

        return [
            'ready' => false,
            'qr' => null,
            'user' => null,
            'error' => 'Local WhatsApp Gateway tidak aktif. Pastikan menjalankan "node whatsapp-service.js".'
        ];
    }

    /**
     * Format nomor telepon ke standar internasional Indonesia (62)
     */
    private function formatPhoneNumber(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        if (!str_starts_with($phone, '62') && strlen($phone) >= 9) {
            $phone = '62' . $phone;
        }

        return $phone;
    }
}
