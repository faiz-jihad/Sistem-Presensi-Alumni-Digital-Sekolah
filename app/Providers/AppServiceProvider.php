<?php

namespace App\Providers;

use App\Models\PresensiSession;
use App\Policies\AttendanceSessionPolicy;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Set locale Bahasa Indonesia untuk seluruh aplikasi (termasuk Filament)
        App::setLocale('id');
        App::setFallbackLocale('id');

        // Set Carbon ke Bahasa Indonesia agar tanggal tampil dalam format Indonesia
        Carbon::setLocale('id');

        // Configure FileUpload placeholder globally
        \Filament\Forms\Components\FileUpload::configureUsing(function (\Filament\Forms\Components\FileUpload $component) {
            $component->placeholder('<span class="filepond--label-action">unggah</span> atau drop file anda');
        });

        // ─── Policy Registration ────────────────────────────────────────────
        Gate::policy(PresensiSession::class, AttendanceSessionPolicy::class);
        Gate::policy(\App\Models\Schedule::class, \App\Policies\MasterDataPolicy::class);
        Gate::policy(\App\Models\Subject::class, \App\Policies\MasterDataPolicy::class);
        Gate::policy(\App\Models\ClassHour::class, \App\Policies\MasterDataPolicy::class);
        Gate::policy(\App\Models\StudentClass::class, \App\Policies\MasterDataPolicy::class);
        Gate::policy(\App\Models\AcademicYear::class, \App\Policies\MasterDataPolicy::class);
        Gate::policy(\App\Models\Teacher::class, \App\Policies\MasterDataPolicy::class);

        // ─── Database Notification Observer for Firebase Push ────────────────
        \Illuminate\Notifications\DatabaseNotification::created(function ($notification) {
            $user = $notification->notifiable;
            
            if ($user instanceof \App\Models\User) {
                $data = $notification->data;
                $title = $data['title'] ?? 'Notifikasi Baru';
                $body = $data['body'] ?? '';
                
                try {
                    $firebaseService = app(\App\Services\FirebaseNotificationService::class);
                    $firebaseService->sendPushNotification($user, $title, $body, [
                        'notification_id' => $notification->id,
                    ]);
                } catch (\Exception $e) {
                    logger()->error('Failed to send FCM push notification from DatabaseNotification observer: ' . $e->getMessage());
                }
            }
        });

        // ─── Model Observers for Automated Notifications ────────────────────
        // 1. PresensiSession (Ketika Guru membuka kelas) -> Kirim ke semua Siswa di kelas tersebut
        \App\Models\PresensiSession::created(function ($session) {
            $class = $session->class;
            if ($class) {
                $students = $class->students;
                $subjectName = $session->schedule?->subject?->name ?? 'Mata Pelajaran';
                
                foreach ($students as $student) {
                    $studentUser = \App\Models\User::where('role', 'student')
                        ->where(function ($q) use ($student) {
                            $q->where('email', $student->nis)
                              ->orWhere('name', $student->name);
                        })->first();
                        
                    if ($studentUser) {
                        \Filament\Notifications\Notification::make()
                            ->title('Sesi Presensi Dibuka')
                            ->body("Kelas {$subjectName} untuk kelas Anda telah dibuka. Silakan lakukan presensi!")
                            ->info()
                            ->sendToDatabase($studentUser);
                    }
                }
            }
        });

        // 2. StudentAttendance (Ketika presensi dicatat) -> Kirim ke Siswa & Orang Tua/Wali
        \App\Models\StudentAttendance::saved(function ($attendance) {
            $student = $attendance->student;
            if ($student) {
                $statusVal = $attendance->status instanceof \App\Enums\AttendanceStatus ? $attendance->status->value : $attendance->status;
                $statusLabel = match ($statusVal) {
                    'present' => 'Hadir',
                    'late' => 'Terlambat',
                    'permission' => 'Izin',
                    'sick' => 'Sakit',
                    'absent' => 'Alpha',
                    default => $statusVal,
                };
                
                $title = 'Pemberitahuan Kehadiran';
                $body = "Siswa {$student->name} tercatat {$statusLabel} pada tanggal " . \Carbon\Carbon::parse($attendance->date)->translatedFormat('d F Y') . ".";
                
                // Kirim ke Orang Tua
                $parentUser = $student->parent;
                if ($parentUser) {
                    \Filament\Notifications\Notification::make()
                        ->title($title)
                        ->body($body)
                        ->info()
                        ->sendToDatabase($parentUser);
                }

                // Kirim ke Siswa
                $studentUser = \App\Models\User::where('role', 'student')
                    ->where(function ($q) use ($student) {
                        $q->where('email', $student->nis)
                          ->orWhere('name', $student->name);
                    })->first();
                    
                if ($studentUser) {
                    \Filament\Notifications\Notification::make()
                        ->title($title)
                        ->body("Presensi Anda tercatat {$statusLabel} pada tanggal " . \Carbon\Carbon::parse($attendance->date)->translatedFormat('d F Y') . ".")
                        ->success()
                        ->sendToDatabase($studentUser);
                }
            }
        });

        // 3. Alumni (Ketika akun alumni diverifikasi admin) -> Kirim ke Alumni terkait
        \App\Models\Alumni::updated(function ($alumni) {
            if ($alumni->isDirty('verification_status') && $alumni->verification_status === 'verified') {
                $user = $alumni->user;
                if ($user) {
                    \Filament\Notifications\Notification::make()
                        ->title('Akun Alumni Terverifikasi')
                        ->body('Selamat! Akun alumni Anda telah terverifikasi oleh Admin.')
                        ->success()
                        ->sendToDatabase($user);
                }
            }
        });

        // 4. AlumniEvent (Ketika kegiatan alumni disetujui admin) -> Kirim ke semua Alumni
        \App\Models\AlumniEvent::saved(function ($event) {
            if (($event->wasRecentlyCreated || $event->isDirty('approval_status')) && $event->approval_status === 'approved') {
                $alumniUsers = \App\Models\User::where('role', 'alumni')->get();
                
                foreach ($alumniUsers as $user) {
                    \Filament\Notifications\Notification::make()
                        ->title('Kegiatan Alumni Baru')
                        ->body("Ada kegiatan baru: {$event->title} pada tanggal " . \Carbon\Carbon::parse($event->event_date)->translatedFormat('d F Y') . ".")
                        ->info()
                        ->sendToDatabase($user);
                }
            }
        });

        // 5. JobVacancy (Ketika lowongan kerja disetujui/aktif) -> Kirim ke semua Alumni
        \App\Models\JobVacancy::saved(function ($job) {
            if (($job->wasRecentlyCreated || $job->isDirty('is_active')) && $job->is_active) {
                $alumniUsers = \App\Models\User::where('role', 'alumni')->get();
                
                foreach ($alumniUsers as $user) {
                    \Filament\Notifications\Notification::make()
                        ->title('Lowongan Kerja Baru')
                        ->body("Lowongan kerja baru tersedia: {$job->title} di {$job->company_name}.")
                        ->info()
                        ->sendToDatabase($user);
                }
            }
        });

        // 6. Schedule (Ketika jadwal mengajar dibuat/diubah) -> Kirim ke Guru pengampu
        \App\Models\Schedule::saved(function ($schedule) {
            $teacher = $schedule->teacher;
            if ($teacher && $teacher->user) {
                $subjectName = $schedule->subject?->name ?? 'Mata Pelajaran';
                $className = $schedule->class?->name ?? 'Kelas';
                
                \Filament\Notifications\Notification::make()
                    ->title('Jadwal Mengajar Diperbarui')
                    ->body("Anda memiliki jadwal mengajar: {$subjectName} di kelas {$className} untuk hari " . ($schedule->day ? $schedule->day->label() : '') . ".")
                    ->info()
                    ->sendToDatabase($teacher->user);
            }
        });

        // ─── Desktop Browser Native Notifications Hook ───────────────────────
        \Filament\Support\Facades\FilamentView::registerRenderHook(
            \Filament\View\PanelsRenderHook::BODY_END,
            fn () => new \Illuminate\Support\HtmlString("
                <script>
                    document.addEventListener('DOMContentLoaded', () => {
                        console.log('[FCM Web Debug] Script loaded. Permission status:', typeof Notification !== 'undefined' ? Notification.permission : 'Not supported');

                        const requestNotificationPermission = () => {
                            if (typeof Notification !== 'undefined' && Notification.permission === 'default') {
                                console.log('[FCM Web Debug] Requesting notification permission...');
                                Notification.requestPermission().then(permission => {
                                    console.log('[FCM Web Debug] Permission response:', permission);
                                });
                            }
                        };
                        document.addEventListener('click', requestNotificationPermission, { once: true });
                        document.addEventListener('keydown', requestNotificationPermission, { once: true });

                        window.addEventListener('notificationSent', (event) => {
                            const notification = event.detail.notification;
                            if (!notification) return;

                            console.log('[FCM Web Debug] Notification event captured:', notification);

                            const title = notification.title || 'Notifikasi Baru';
                            const body = notification.body || '';

                            if (typeof Notification !== 'undefined' && Notification.permission === 'granted') {
                                try {
                                    console.log('[FCM Web Debug] Triggering OS native desktop notification...');
                                    new Notification(title, {
                                        body: body
                                    });
                                } catch (e) {
                                    console.error('[FCM Web Debug] Failed to trigger desktop notification:', e);
                                }
                            } else {
                                console.warn('[FCM Web Debug] Notification permission is not granted. Current state:', typeof Notification !== 'undefined' ? Notification.permission : 'Not supported');
                            }
                        });
                    });
                </script>
            ")
        );
    }
}
