<?php

namespace App\Observers;

use App\Mail\JobVacancyApprovedMail;
use App\Models\JobVacancy;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class JobVacancyObserver
{
    public function created(JobVacancy $job): void
    {
        if ($job->is_active) {
            return;
        }

        try {
            $admins = $this->adminRecipients($job->school_id);

            if ($admins->isEmpty()) {
                return;
            }

            Notification::make()
                ->title('Pengajuan Lowongan Kerja Baru')
                ->body("Ada pengajuan lowongan kerja baru: {$job->title} di {$job->company_name}. Menunggu persetujuan.")
                ->info()
                ->sendToDatabase($admins);
        } catch (\Throwable $exception) {
            Log::error('Gagal mengirim notifikasi pengajuan lowongan kerja.', [
                'job_id' => $job->id,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    public function updated(JobVacancy $job): void
    {
        if (! $job->wasChanged('is_active') || ! $job->is_active) {
            return;
        }

        try {
            $poster = User::find($job->posted_by);

            if ($poster) {
                Notification::make()
                    ->title('Lowongan Kerja Disetujui')
                    ->body("Selamat! Lowongan kerja '{$job->title}' yang Anda ajukan telah disetujui dan aktif.")
                    ->success()
                    ->sendToDatabase($poster);

                $this->sendApprovedEmail($job, $poster);
            }

            $alumniUsers = User::where('role', 'alumni')->get();

            foreach ($alumniUsers as $user) {
                if ($poster && $user->id === $poster->id) {
                    continue;
                }

                Notification::make()
                    ->title('Lowongan Kerja Baru')
                    ->body("Lowongan kerja baru tersedia: {$job->title} di {$job->company_name}.")
                    ->info()
                    ->sendToDatabase($user);
            }
        } catch (\Throwable $exception) {
            Log::error('Gagal memproses notifikasi status lowongan kerja.', [
                'job_id' => $job->id,
                'error' => $exception->getMessage(),
            ]);
        }
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

    private function sendApprovedEmail(JobVacancy $job, User $poster): void
    {
        if (! $poster->email || ! filter_var($poster->email, FILTER_VALIDATE_EMAIL)) {
            return;
        }

        try {
            Mail::to($poster->email)->send(new JobVacancyApprovedMail($job));
        } catch (\Throwable $exception) {
            Log::error('Gagal mengirim email persetujuan lowongan kerja.', [
                'job_id' => $job->id,
                'email' => $poster->email,
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
