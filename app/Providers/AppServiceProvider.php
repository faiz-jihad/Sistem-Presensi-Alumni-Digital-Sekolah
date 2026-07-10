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
        $notificationCreatedHandler = function ($notification) {
            $user = $notification->notifiable;
            
            if ($user instanceof \App\Models\User) {
                $data = $notification->data;
                $title = $data['title'] ?? 'Notifikasi Baru';
                $body = $data['body'] ?? '';
                
                // Bersihkan tag HTML (seperti markdown/formatting text dari Filament)
                $title = strip_tags($title);
                $body = strip_tags($body);
                
                try {
                    $user->notify(new \App\Notifications\FilamentWebPushNotification($title, $body, [
                        'notification_id' => $notification->id,
                        'url' => '/admin',
                    ]));
                } catch (\Exception $e) {
                    logger()->error('Failed to send Web Push notification from DatabaseNotification observer: ' . $e->getMessage());
                }

                try {
                    $firebaseService = app(\App\Services\FirebaseNotificationService::class);
                    $payload = $data['data'] ?? [];
                    if (! is_array($payload)) {
                        $payload = [];
                    }
                    $payload['notification_id'] = $notification->id;

                    $firebaseService->sendPushNotification($user, $title, $body, $payload);
                } catch (\Exception $e) {
                    logger()->error('Failed to send FCM push notification from DatabaseNotification observer: ' . $e->getMessage());
                }
            }
        };

        \Illuminate\Notifications\DatabaseNotification::created($notificationCreatedHandler);

        if (class_exists(\Filament\Notifications\Models\DatabaseNotification::class)) {
            \Filament\Notifications\Models\DatabaseNotification::created($notificationCreatedHandler);
        }

        // ─── Model Observers for Automated Notifications ────────────────────
        
        // 1. PresensiSession Observers
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
                            ->title('Sesi Presensi Dibuka 🔔')
                            ->body("Sesi presensi mata pelajaran {$subjectName} untuk kelas Anda telah dibuka. Silakan lakukan presensi!")
                            ->info()
                            ->sendToDatabase($studentUser);
                    }
                }
            }
        });

        \App\Models\PresensiSession::updated(function ($session) {
            if ($session->isDirty('status') && $session->status === \App\Enums\SessionStatus::Closed) {
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
                                ->title('Sesi Presensi Ditutup 🔏')
                                ->body("Sesi presensi mata pelajaran {$subjectName} untuk kelas Anda telah ditutup. Terima kasih.")
                                ->success()
                                ->sendToDatabase($studentUser);
                        }
                    }
                }
            }
        });

        // 2. StudentAttendance Observers
        \App\Models\StudentAttendance::saved(function ($attendance) {
            $student = $attendance->student;
            if ($student) {
                $statusVal = $attendance->status instanceof \App\Enums\AttendanceStatus ? $attendance->status->value : $attendance->status;
                $statusLabel = match ($statusVal) {
                    'present'    => 'Hadir',
                    'late'       => 'Terlambat',
                    'permission' => 'Izin',
                    'sick'       => 'Sakit',
                    'absent'     => 'Alpha',
                    default      => $statusVal,
                };
                
                $title = 'Laporan Kehadiran Siswa';
                $body = "Siswa {$student->name} tercatat {$statusLabel} pada tanggal " . \Carbon\Carbon::parse($attendance->date)->translatedFormat('d F Y') . ".";
                
                // Kirim ke Orang Tua / Wali
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

        // 3. Alumni Observers
        \App\Models\Alumni::created(function ($alumni) {
            // Notifikasi registrasi alumni baru ke Admin / Super Admin
            $admins = \App\Models\User::role(['admin', 'super_admin'])->get();
            if ($admins->isNotEmpty()) {
                \Filament\Notifications\Notification::make()
                    ->title('Pendaftaran Alumni Baru 🔔')
                    ->body("Alumni **{$alumni->name}** (Lulusan {$alumni->graduation_year}) baru saja mendaftar. Menunggu verifikasi admin!")
                    ->info()
                    ->sendToDatabase($admins);
            }
        });

        \App\Models\Alumni::updated(function ($alumni) {
            if ($alumni->isDirty('verification_status')) {
                $user = $alumni->user;
                if ($user) {
                    if ($alumni->verification_status === 'verified') {
                        \Filament\Notifications\Notification::make()
                            ->title('Akun Alumni Terverifikasi ✅')
                            ->body('Selamat! Akun alumni Anda telah terverifikasi oleh Admin. Anda sekarang dapat mengakses seluruh fitur alumni.')
                            ->success()
                            ->sendToDatabase($user);
                    } elseif ($alumni->verification_status === 'rejected') {
                        $reason = $alumni->verification_notes ?? 'Tidak ada alasan khusus yang diberikan.';
                        \Filament\Notifications\Notification::make()
                            ->title('Pendaftaran Alumni Ditolak ❌')
                            ->body("Maaf, pendaftaran alumni Anda ditolak. Alasan: {$reason}")
                            ->danger()
                            ->sendToDatabase($user);
                    }
                }
            }
        });

        // 4. AlumniEvent Observers
        \App\Models\AlumniEvent::created(function ($event) {
            // Jika diajukan oleh alumni (butuh approval)
            if ($event->approval_status === 'pending') {
                $admins = \App\Models\User::role(['admin', 'super_admin'])->get();
                if ($admins->isNotEmpty()) {
                    \Filament\Notifications\Notification::make()
                        ->title('Pengajuan Kegiatan Alumni Baru 🗓️')
                        ->body("Ada pengajuan kegiatan baru: **{$event->title}** oleh alumni. Silakan periksa untuk verifikasi.")
                        ->info()
                        ->sendToDatabase($admins);
                }
            }
        });

        \App\Models\AlumniEvent::updated(function ($event) {
            if ($event->isDirty('approval_status')) {
                // Notifikasi ke alumni pengaju ketika statusnya disetujui / ditolak
                $poster = \App\Models\User::find($event->posted_by);
                
                if ($event->approval_status === 'approved') {
                    if ($poster) {
                        \Filament\Notifications\Notification::make()
                            ->title('Pengajuan Kegiatan Disetujui 🎉')
                            ->body("Selamat! Pengajuan kegiatan Anda '**{$event->title}**' telah disetujui oleh admin.")
                            ->success()
                            ->sendToDatabase($poster);
                    }

                    // Notifikasi ke seluruh alumni tentang event baru
                    $alumniUsers = \App\Models\User::where('role', 'alumni')->get();
                    foreach ($alumniUsers as $user) {
                        if ($poster && $user->id === $poster->id) continue;
                        \Filament\Notifications\Notification::make()
                            ->title('Kegiatan Alumni Baru 📅')
                            ->body("Ada kegiatan alumni baru: **{$event->title}** pada tanggal " . \Carbon\Carbon::parse($event->event_date)->translatedFormat('d F Y') . ".")
                            ->info()
                            ->sendToDatabase($user);
                    }
                } elseif ($event->approval_status === 'rejected') {
                    if ($poster) {
                        \Filament\Notifications\Notification::make()
                            ->title('Pengajuan Kegiatan Ditolak ❌')
                            ->body("Maaf, pengajuan kegiatan Anda '**{$event->title}**' ditolak oleh admin.")
                            ->danger()
                            ->sendToDatabase($poster);
                    }
                }
            }
        });

        // 5. JobVacancy Observers
        \App\Models\JobVacancy::created(function ($job) {
            // Notifikasi pengajuan loker oleh alumni
            if (!$job->is_active) {
                $admins = \App\Models\User::role(['admin', 'super_admin'])->get();
                if ($admins->isNotEmpty()) {
                    \Filament\Notifications\Notification::make()
                        ->title('Pengajuan Lowongan Kerja Baru 💼')
                        ->body("Ada pengajuan lowongan kerja baru: **{$job->title}** di **{$job->company_name}**. Menunggu persetujuan.")
                        ->info()
                        ->sendToDatabase($admins);
                }
            }
        });

        \App\Models\JobVacancy::updated(function ($job) {
            if ($job->isDirty('is_active') && $job->is_active) {
                // Notifikasi ke poster alumni
                $poster = \App\Models\User::find($job->posted_by);
                if ($poster) {
                    \Filament\Notifications\Notification::make()
                        ->title('Lowongan Kerja Disetujui 🎉')
                        ->body("Selamat! Lowongan kerja '**{$job->title}**' yang Anda ajukan telah disetujui dan aktif.")
                        ->success()
                        ->sendToDatabase($poster);
                }

                // Notifikasi ke seluruh alumni
                $alumniUsers = \App\Models\User::where('role', 'alumni')->get();
                foreach ($alumniUsers as $user) {
                    if ($poster && $user->id === $poster->id) continue;
                    \Filament\Notifications\Notification::make()
                        ->title('Lowongan Kerja Baru 💼')
                        ->body("Lowongan kerja baru tersedia: **{$job->title}** di **{$job->company_name}**.")
                        ->info()
                        ->sendToDatabase($user);
                }
            }
        });

        // 6. Schedule Observers
        \App\Models\Schedule::saved(function ($schedule) {
            $teacher = $schedule->teacher;
            if ($teacher && $teacher->user) {
                $subjectName = $schedule->subject?->name ?? 'Mata Pelajaran';
                $className = $schedule->class?->name ?? 'Kelas';
                
                \Filament\Notifications\Notification::make()
                    ->title('Jadwal Mengajar Diperbarui 🗓️')
                    ->body("Anda memiliki jadwal mengajar baru/diperbarui: {$subjectName} di kelas {$className} untuk hari " . ($schedule->day ? $schedule->day->label() : '') . ".")
                    ->info()
                    ->sendToDatabase($teacher->user);
            }
        });

        // Auto notify admin users for important Filament model changes.
        foreach ([
            \App\Models\AcademicYear::class,
            \App\Models\Alumni::class,
            \App\Models\AlumniEvent::class,
            \App\Models\ClassHourPackage::class,
            \App\Models\Export::class,
            \App\Models\JobVacancy::class,
            \App\Models\Package::class,
            \App\Models\PresensiSession::class,
            \App\Models\Schedule::class,
            \App\Models\School::class,
            \App\Models\SchoolClass::class,
            \App\Models\Semester::class,
            \App\Models\Student::class,
            \App\Models\StudentAttendance::class,
            \App\Models\StudentClass::class,
            \App\Models\Subject::class,
            \App\Models\Teacher::class,
            \App\Models\User::class,
        ] as $modelClass) {
            if (! class_exists($modelClass)) {
                continue;
            }

            $modelClass::created(fn ($model) => app(\App\Services\FilamentChangeNotificationService::class)->notifyModelChanged($model, 'created'));
            $modelClass::updated(fn ($model) => app(\App\Services\FilamentChangeNotificationService::class)->notifyModelChanged($model, 'updated'));
            $modelClass::deleted(fn ($model) => app(\App\Services\FilamentChangeNotificationService::class)->notifyModelChanged($model, 'deleted'));
        }
        // ─── Desktop Browser Native Notifications Hook ───────────────────────
        \Filament\Support\Facades\FilamentView::registerRenderHook(
            \Filament\View\PanelsRenderHook::BODY_END,
            fn () => view('filament.components.firebase-script')
        );

        // ─── Premium Sidebar CSS Style Hook ───────────────────────────
        \Filament\Support\Facades\FilamentView::registerRenderHook(
            \Filament\View\PanelsRenderHook::HEAD_END,
            fn () => view('filament.components.premium-sidebar-css')
        );

        // ─── Auto-download Firebase scripts locally to bypass AdBlockers ───────
        $jsDir = public_path('js');
        if (!file_exists($jsDir)) {
            @mkdir($jsDir, 0755, true);
        }
        
        $firebaseScripts = [
            'core-app.js' => 'https://cdn.jsdelivr.net/npm/firebase@10.8.0/firebase-app-compat.js',
            'user-session.js' => 'https://cdn.jsdelivr.net/npm/firebase@10.8.0/firebase-auth-compat.js',
            'core-msg.js' => 'https://cdn.jsdelivr.net/npm/firebase@10.8.0/firebase-messaging-compat.js'
        ];
        
        foreach ($firebaseScripts as $name => $url) {
            $path = $jsDir . '/' . $name;
            if (!file_exists($path)) {
                try {
                    // Set timeout for fast load
                    $ctx = stream_context_create([
                        'http' => [
                            'timeout' => 5, // seconds
                        ]
                    ]);
                    $content = @file_get_contents($url, false, $ctx);
                    if ($content) {
                        @file_put_contents($path, $content);
                    }
                } catch (\Exception $e) {
                    // Fail silently
                }
            }
        }

        // ─── Google Sign-In Button on Filament Login Page ────────────────────
        \Filament\Support\Facades\FilamentView::registerRenderHook(
            \Filament\View\PanelsRenderHook::AUTH_LOGIN_FORM_AFTER,
            fn () => view('filament.components.google-login-button')
        );
    }
}
