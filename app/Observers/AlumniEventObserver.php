<?php

namespace App\Observers;

use App\Mail\AlumniEventApprovedMail;
use App\Mail\AlumniEventRejectedMail;
use App\Models\AlumniEvent;
use App\Models\User;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AlumniEventObserver
{
    public function created(AlumniEvent $event): void
    {
        if ($event->approval_status !== 'pending') {
            return;
        }

        try {
            $admins = $this->adminRecipients($event->school_id);

            if ($admins->isEmpty()) {
                return;
            }

            Notification::make()
                ->title('Pengajuan Kegiatan Alumni Baru')
                ->body("Ada pengajuan kegiatan baru: {$event->title} oleh alumni. Silakan periksa untuk verifikasi.")
                ->info()
                ->sendToDatabase($admins);
        } catch (\Throwable $exception) {
            Log::error('Gagal mengirim notifikasi pengajuan kegiatan alumni.', [
                'event_id' => $event->id,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    public function updated(AlumniEvent $event): void
    {
        if (! $event->wasChanged('approval_status')) {
            return;
        }

        try {
            $poster = User::find($event->posted_by);

            if ($event->approval_status === 'approved') {
                $this->notifyApproved($event, $poster);
            } elseif ($event->approval_status === 'rejected') {
                $this->notifyRejected($event, $poster);
            }
        } catch (\Throwable $exception) {
            Log::error('Gagal memproses notifikasi status kegiatan alumni.', [
                'event_id' => $event->id,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    private function notifyApproved(AlumniEvent $event, ?User $poster): void
    {
        if ($poster) {
            Notification::make()
                ->title('Pengajuan Kegiatan Disetujui')
                ->body("Selamat! Pengajuan kegiatan Anda '{$event->title}' telah disetujui oleh admin.")
                ->success()
                ->sendToDatabase($poster);

            $this->sendEmail($poster->email, new AlumniEventApprovedMail($event), 'Gagal mengirim email persetujuan kegiatan alumni.', ['event_id' => $event->id]);
        }

        $alumniUsers = User::where('role', 'alumni')->get();

        foreach ($alumniUsers as $user) {
            if ($poster && $user->id === $poster->id) {
                continue;
            }

            Notification::make()
                ->title('Kegiatan Alumni Baru')
                ->body("Ada kegiatan alumni baru: {$event->title} pada tanggal " . Carbon::parse($event->event_date)->translatedFormat('d F Y') . '.')
                ->info()
                ->sendToDatabase($user);
        }
    }

    private function notifyRejected(AlumniEvent $event, ?User $poster): void
    {
        if (! $poster) {
            return;
        }

        Notification::make()
            ->title('Pengajuan Kegiatan Ditolak')
            ->body("Maaf, pengajuan kegiatan Anda '{$event->title}' ditolak oleh admin.")
            ->danger()
            ->sendToDatabase($poster);

        $this->sendEmail($poster->email, new AlumniEventRejectedMail($event), 'Gagal mengirim email penolakan kegiatan alumni.', ['event_id' => $event->id]);
    }

    private function adminRecipients(?int $schoolId)
    {
        return User::query()
            ->whereIn('role', ['super_admin', 'admin'])
            ->where('status', 'active')
            ->where(function ($query) use ($schoolId) {
                $query->where('role', 'super_admin')
                    ->orWhere(function ($query) use ($schoolId) {
                        $query->where('role', 'admin')
                            ->where('school_id', $schoolId);
                    });
            })
            ->get();
    }

    private function sendEmail(?string $email, object $mailable, string $message, array $context): void
    {
        if (! $email || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return;
        }

        try {
            Mail::to($email)->send($mailable);
        } catch (\Throwable $exception) {
            Log::error($message, [
                ...$context,
                'email' => $email,
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
