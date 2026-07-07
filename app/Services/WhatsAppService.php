<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    private string $apiUrl;
    private string $apiToken;

    public function __construct()
    {
        $this->apiUrl = config('services.whatsapp.api_url', env('WHATSAPP_API_URL', 'https://api.fonnte.com/send'));
        $this->apiToken = config('services.whatsapp.api_token', env('WHATSAPP_API_TOKEN', 'your-token-here'));
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

        // Bersihkan format nomor agar dimulai dengan 62 (atau format yang sesuai)
        $to = $this->formatPhoneNumber($to);

        if (empty($to)) {
            Log::warning('WhatsApp recipient became empty after formatting.');
            return false;
        }

        // Fallback jika token masih default/placeholder
        if (empty($this->apiToken) || $this->apiToken === 'your-token-here' || $this->apiToken === 'token-kamu-disini') {
            Log::info("WhatsApp (Simulated) to {$to}: {$message}");
            return true;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $this->apiToken,
            ])->asForm()->post($this->apiUrl, [
                'target' => $to,
                'message' => $message,
                'countryCode' => '62',
            ]);

            if ($response->successful()) {
                $responseData = $response->json();
                if (isset($responseData['status']) && $responseData['status'] === false) {
                    Log::error("WhatsApp Fonnte failed to {$to}: " . ($responseData['reason'] ?? $response->body()));
                    return false;
                }
                Log::info("WhatsApp sent successfully to {$to}. Response: " . $response->body());
                return true;
            }

            Log::error("WhatsApp failed to {$to}. Response: " . $response->body());
            return false;
        } catch (\Exception $e) {
            Log::error("WhatsApp exception to {$to}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Format nomor telepon ke standar internasional Indonesia (62)
     */
    private function formatPhoneNumber(string $phone): string
    {
        // Hapus karakter non-digit
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Jika diawali dengan '0', ganti dengan '62'
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        // Jika belum diawali '62' dan panjangnya wajar, asumsikan nomor lokal
        if (!str_starts_with($phone, '62') && strlen($phone) >= 9) {
            $phone = '62' . $phone;
        }

        return $phone;
    }
}
