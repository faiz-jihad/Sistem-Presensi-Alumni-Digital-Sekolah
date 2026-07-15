<?php

namespace App\Observers;

use App\Mail\AlumniAccountVerifiedMail;
use App\Models\Alumni;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AlumniObserver
{
    public function updated(Alumni $alumni): void
    {
        if (! $alumni->wasChanged('verification_status')) {
            return;
        }

        if ($alumni->verification_status === 'verified') {
            if ($alumni->user) {
                try {
                    Notification::make()
                        ->title('Akun Alumni Terverifikasi')
                        ->body('Selamat! Akun alumni Anda telah terverifikasi oleh Admin. Anda sekarang dapat mengakses seluruh fitur alumni.')
                        ->success()
                        ->sendToDatabase($alumni->user);
                } catch (\Throwable $exception) {
                    Log::error('Gagal menyimpan notifikasi verifikasi alumni.', [
                        'alumni_id' => $alumni->id,
                        'error' => $exception->getMessage(),
                    ]);
                }
            }

            $this->sendVerifiedEmail($alumni);

            return;
        }

        if ($alumni->verification_status === 'rejected' && $alumni->user) {
            try {
                $reason = $alumni->verification_notes ?? 'Tidak ada alasan khusus yang diberikan.';
                Notification::make()
                    ->title('Pendaftaran Alumni Ditolak')
                    ->body("Maaf, pendaftaran alumni Anda ditolak. Alasan: {$reason}")
                    ->danger()
                    ->sendToDatabase($alumni->user);
            } catch (\Throwable $exception) {
                Log::error('Gagal menyimpan notifikasi penolakan alumni.', [
                    'alumni_id' => $alumni->id,
                    'error' => $exception->getMessage(),
                ]);
            }
        }
    }

    private function sendVerifiedEmail(Alumni $alumni): void
    {
        $recipientEmail = $alumni->email ?: $alumni->user?->email;

        if (! $recipientEmail || ! filter_var($recipientEmail, FILTER_VALIDATE_EMAIL)) {
            Log::warning('Email verifikasi alumni tidak dikirim karena alamat email tidak valid.', [
                'alumni_id' => $alumni->id,
                'email' => $recipientEmail,
            ]);

            return;
        }

        try {
            Mail::to($recipientEmail)->send(new AlumniAccountVerifiedMail($alumni));

            Log::info('Email verifikasi akun alumni berhasil dikirim.', [
                'alumni_id' => $alumni->id,
                'email' => $recipientEmail,
            ]);
        } catch (\Throwable $exception) {
            Log::error('Gagal mengirim email verifikasi akun alumni.', [
                'alumni_id' => $alumni->id,
                'email' => $recipientEmail,
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
