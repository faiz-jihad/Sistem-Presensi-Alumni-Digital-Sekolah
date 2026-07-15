<?php

namespace App\Services;

use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class FilamentChangeNotificationService
{
    public function notifyModelChanged(Model $model, string $event): void
    {
        if (! app()->runningInConsole() && ! auth()->check()) {
            return;
        }

        $recipients = $this->adminRecipientsFor($model);

        if ($recipients->isEmpty()) {
            return;
        }

        $label = $this->modelLabel($model);
        $record = $this->recordLabel($model);
        $eventLabel = $this->eventLabel($event);
        $actor = auth()->user()?->name ?? 'Sistem';

        $notification = Notification::make()
            ->title("{$label} {$eventLabel}")
            ->body("{$record} {$eventLabel} oleh {$actor}.");

        match ($event) {
            'created' => $notification->success(),
            'deleted' => $notification->danger(),
            default => $notification->info(),
        };

        $notification->sendToDatabase($recipients);
    }

    public function sendToUsers(iterable $users, string $title, string $body, string $status = 'info'): void
    {
        $recipients = collect($users)->filter();

        if ($recipients->isEmpty()) {
            return;
        }

        $notification = Notification::make()
            ->title($title)
            ->body($body);

        match ($status) {
            'success' => $notification->success(),
            'warning' => $notification->warning(),
            'danger' => $notification->danger(),
            default => $notification->info(),
        };

        $notification->sendToDatabase($recipients);
    }

    private function adminRecipientsFor(Model $model): Collection
    {
        $schoolId = $this->schoolIdFor($model);

        return User::query()
            ->whereIn('role', ['super_admin', 'admin'])
            ->where('status', 'active')
            ->when($schoolId, function ($query) use ($schoolId) {
                $query->where(function ($query) use ($schoolId) {
                    $query->where('role', 'super_admin')
                        ->orWhere(function ($query) use ($schoolId) {
                            $query->where('role', 'admin')
                                ->where('school_id', $schoolId);
                        });
                });
            })
            ->get();
    }

    private function schoolIdFor(Model $model): ?int
    {
        if ($model instanceof \App\Models\School) {
            return $model->getKey();
        }

        $schoolId = $model->getAttribute('school_id');

        if ($schoolId) {
            return (int) $schoolId;
        }

        $student = $model->getRelationValue('student') ?? null;
        if ($student?->school_id) {
            return (int) $student->school_id;
        }

        $class = $model->getRelationValue('class') ?? null;
        if ($class?->school_id) {
            return (int) $class->school_id;
        }

        return null;
    }

    private function modelLabel(Model $model): string
    {
        return match ($model::class) {
            \App\Models\AcademicYear::class => 'Tahun ajaran',
            \App\Models\Alumni::class => 'Alumni',
            \App\Models\AlumniEvent::class => 'Kegiatan alumni',
            \App\Models\ClassHour::class => 'Jam pelajaran',
            \App\Models\ClassHourPackage::class => 'Paket jam pelajaran',
            \App\Models\Export::class => 'Laporan',
            \App\Models\JobVacancy::class => 'Lowongan kerja',
            \App\Models\PresensiSession::class => 'Sesi presensi',
            \App\Models\Schedule::class => 'Jadwal',
            \App\Models\School::class => 'Sekolah',
            \App\Models\SchoolClass::class => 'Kelas sekolah',
            \App\Models\Semester::class => 'Semester',
            \App\Models\Student::class => 'Siswa',
            \App\Models\StudentAttendance::class => 'Presensi siswa',
            \App\Models\StudentClass::class => 'Kelas siswa',
            \App\Models\Subject::class => 'Mata pelajaran',
            \App\Models\Teacher::class => 'Guru',
            \App\Models\User::class => 'User',
            default => class_basename($model),
        };
    }

    private function recordLabel(Model $model): string
    {
        foreach (['name', 'title', 'email', 'nis', 'nip', 'npsn', 'company_name', 'file_name'] as $attribute) {
            $value = $model->getAttribute($attribute);

            if (filled($value)) {
                return (string) $value;
            }
        }

        return '#' . ($model->getKey() ?? 'baru');
    }

    private function eventLabel(string $event): string
    {
        return match ($event) {
            'created' => 'ditambahkan',
            'updated' => 'diperbarui',
            'deleted' => 'dihapus',
            default => 'berubah',
        };
    }
}