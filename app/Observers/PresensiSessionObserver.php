<?php

namespace App\Observers;

use App\Enums\SessionStatus;
use App\Models\PresensiSession;
use App\Models\Student;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class PresensiSessionObserver
{
    public function created(PresensiSession $session): void
    {
        $this->notifyStudents($session, 'Sesi Presensi Dibuka', 'Sesi presensi mata pelajaran %s untuk kelas Anda telah dibuka. Silakan lakukan presensi!', 'info');
    }

    public function updated(PresensiSession $session): void
    {
        if (! $session->wasChanged('status') || $session->status !== SessionStatus::Closed) {
            return;
        }

        $this->notifyStudents($session, 'Sesi Presensi Ditutup', 'Sesi presensi mata pelajaran %s untuk kelas Anda telah ditutup. Terima kasih.', 'success');
    }

    private function notifyStudents(PresensiSession $session, string $title, string $bodyTemplate, string $status): void
    {
        try {
            $class = $session->class;

            if (! $class) {
                return;
            }

            $subjectName = $session->schedule?->subject?->name ?? 'Mata Pelajaran';

            foreach ($class->students as $student) {
                $studentUser = $this->studentUser($student);

                if (! $studentUser) {
                    continue;
                }

                $notification = Notification::make()
                    ->title($title)
                    ->body(sprintf($bodyTemplate, $subjectName));

                match ($status) {
                    'success' => $notification->success(),
                    'danger' => $notification->danger(),
                    default => $notification->info(),
                };

                $notification->sendToDatabase($studentUser);
            }
        } catch (\Throwable $exception) {
            Log::error('Gagal mengirim notifikasi sesi presensi.', [
                'session_id' => $session->id,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    private function studentUser(Student $student): ?User
    {
        if (! filled($student->nis) && ! filled($student->name)) {
            return null;
        }

        return User::query()
            ->where('role', 'student')
            ->when($student->school_id, fn ($query) => $query->where('school_id', $student->school_id))
            ->where(function ($query) use ($student) {
                $hasIdentifier = false;

                if (filled($student->nis)) {
                    $query->where('email', $student->nis);
                    $hasIdentifier = true;
                }

                if (filled($student->name)) {
                    $hasIdentifier
                        ? $query->orWhere('name', $student->name)
                        : $query->where('name', $student->name);
                }
            })
            ->first();
    }
}


