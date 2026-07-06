<?php

namespace Database\Seeders;

use App\Enums\AttendanceStatus;
use App\Enums\SessionStatus;
use App\Models\PresensiSession;
use App\Models\QrToken;
use App\Models\Schedule;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\Teacher;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PresensiSessionSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('⏳ Memulai seeding Riwayat & Presensi Real-Time (14 Hari + Hari Ini)...');

        $schedules = Schedule::with(['classHour', 'class', 'teacher'])->where('is_active', true)->get();
        if ($schedules->isEmpty()) {
            $this->command->warn('⚠️ Tidak ada jadwal aktif. Jalankan DummyDataSeeder terlebih dahulu.');
            return;
        }

        // Ambil semua siswa per kelas dan simpan di memori untuk kecepatan seeding
        $studentsByClass = [];
        $allClasses = Student::select('class_id')->distinct()->pluck('class_id');
        foreach ($allClasses as $classId) {
            $studentsByClass[$classId] = Student::where('class_id', $classId)->get();
        }

        $sessionCount = 0;
        $attendanceCount = 0;

        // ─── 1. HISTORICAL DATA (14 Hari ke Belakang) ─────────────────────────
        $this->command->info('📈 Mengenerate grafik riwayat presensi 14 hari terakhir...');
        
        for ($d = 14; $d >= 1; $d--) {
            $date = Carbon::today()->subDays($d);
            $dayName = strtolower($date->format('l'));

            // Lewati weekend untuk jadwal sekolah normal
            if (in_array($dayName, ['saturday', 'sunday'])) {
                continue;
            }

            $daySchedules = $schedules->where('day', $dayName);

            foreach ($daySchedules as $schedule) {
                if (!isset($studentsByClass[$schedule->class_id]) || $studentsByClass[$schedule->class_id]->isEmpty()) {
                    continue;
                }

                $students = $studentsByClass[$schedule->class_id];
                $startTime = $schedule->classHour?->start_time ?? '07:00:00';
                $endTime   = $schedule->classHour?->end_time ?? '08:30:00';

                // Buat sesi closed untuk masa lalu
                $session = PresensiSession::updateOrCreate(
                    [
                        'schedule_id' => $schedule->id,
                        'date'        => $date->toDateString(),
                    ],
                    [
                        'school_id'      => $schedule->school_id,
                        'teacher_id'     => $schedule->teacher_id,
                        'opened_by'      => $schedule->teacher?->user_id,
                        'opened_at'      => $date->copy()->setTimeFromTimeString($startTime)->subMinutes(5),
                        'start_time'     => $startTime,
                        'end_time'       => $endTime,
                        'status'         => SessionStatus::Closed->value,
                        'closed_by'      => $schedule->teacher?->user_id,
                        'closed_at'      => $date->copy()->setTimeFromTimeString($endTime),
                        'material_topic' => "Materi Pembelajaran Pertemuan Hari " . $date->translatedFormat('l, d F'),
                        'notes'          => 'Pelajaran berlangsung interaktif dan kondusif.',
                    ]
                );
                $sessionCount++;

                // Generate presensi siswa dengan distribusi realistis
                foreach ($students as $student) {
                    $rand = rand(1, 100);
                    if ($rand <= 85) {
                        $status = AttendanceStatus::Present->value;
                        $note = null;
                        $checkIn = $date->copy()->setTimeFromTimeString($startTime)->subMinutes(rand(0, 10))->toTimeString();
                    } elseif ($rand <= 92) {
                        $status = AttendanceStatus::Late->value;
                        $note = 'Terlambat karena kendala lalu lintas / transportasi';
                        $checkIn = $date->copy()->setTimeFromTimeString($startTime)->addMinutes(rand(10, 25))->toTimeString();
                    } elseif ($rand <= 96) {
                        $status = AttendanceStatus::Permission->value;
                        $note = 'Surat izin keluarga / kegiatan sekolah';
                        $checkIn = null;
                    } elseif ($rand <= 98) {
                        $status = AttendanceStatus::Sick->value;
                        $note = 'Sakit demam / flu dengan surat dokter';
                        $checkIn = null;
                    } else {
                        $status = AttendanceStatus::Absent->value;
                        $note = 'Tanpa keterangan';
                        $checkIn = null;
                    }

                    StudentAttendance::updateOrCreate(
                        [
                            'student_id' => $student->id,
                            'date'       => $date->toDateString(),
                        ],
                        [
                            'school_id'           => $student->school_id,
                            'class_id'            => $student->class_id,
                            'teacher_id'          => $schedule->teacher_id,
                            'presensi_session_id' => $session->id,
                            'status'              => $status,
                            'check_in_time'       => $checkIn,
                            'note'                => $note,
                        ]
                    );
                    $attendanceCount++;
                }
            }
        }
        $this->command->info("✅ Riwayat 14 Hari: {$sessionCount} Sesi & {$attendanceCount} Record Presensi selesai.");

        // ─── 2. REAL-TIME TODAY DATA (Hari Ini - Live Saat Ini!) ──────────────
        $this->command->info('⚡ Mengenerate data presensi REAL-TIME HARI INI (Live Sessions)...');
        
        $todayStr = Carbon::today()->toDateString();
        $todayDayName = strtolower(now()->format('l'));
        
        // Ambil jadwal hari ini (atau semua jadwal jika hari ini weekend, supaya dashboard live tetap aktif!)
        $todaySchedules = $schedules->where('day', $todayDayName);
        if ($todaySchedules->isEmpty()) {
            $todaySchedules = $schedules->take(8); // Ambil 8 jadwal representatif jika weekend
        }

        $liveOpenCount = 0;
        $todayClosedCount = 0;

        foreach ($todaySchedules->values() as $idx => $schedule) {
            if (!isset($studentsByClass[$schedule->class_id]) || $studentsByClass[$schedule->class_id]->isEmpty()) {
                continue;
            }

            $students = $studentsByClass[$schedule->class_id];
            $startTime = $schedule->classHour?->start_time ?? '07:00:00';
            $endTime   = $schedule->classHour?->end_time ?? '08:30:00';

            // Bagi jadwal hari ini menjadi:
            // - 40% Sudah Selesai (CLOSED - Sesi Pagi)
            // - 40% SEDANG BERLANGSUNG RIGHT NOW (OPEN - Live Realtime Dashboard)
            // - 20% Belum Dimulai (SCHEDULED - Siang/Sore)
            
            if ($idx % 3 === 0) {
                // 1. SESI PAGI (CLOSED)
                $session = PresensiSession::updateOrCreate(
                    ['schedule_id' => $schedule->id, 'date' => $todayStr],
                    [
                        'school_id'      => $schedule->school_id,
                        'teacher_id'     => $schedule->teacher_id,
                        'opened_by'      => $schedule->teacher?->user_id,
                        'opened_at'      => Carbon::today()->setTime(7, 0),
                        'start_time'     => '07:00:00',
                        'end_time'       => '08:30:00',
                        'status'         => SessionStatus::Closed->value,
                        'closed_by'      => $schedule->teacher?->user_id,
                        'closed_at'      => Carbon::today()->setTime(8, 30),
                        'material_topic' => 'Sesi Pagi: Pembahasan Kasus dan Latihan Soal',
                        'notes'          => 'Kelas pagi selesai tepat waktu.',
                    ]
                );
                $todayClosedCount++;

                foreach ($students as $student) {
                    StudentAttendance::updateOrCreate(
                        ['student_id' => $student->id, 'date' => $todayStr],
                        [
                            'school_id'           => $student->school_id,
                            'class_id'            => $student->class_id,
                            'teacher_id'          => $schedule->teacher_id,
                            'presensi_session_id' => $session->id,
                            'status'              => AttendanceStatus::Present->value,
                            'check_in_time'       => '06:55:00',
                            'note'                => null,
                        ]
                    );
                }
            } elseif ($idx % 3 === 1) {
                // 2. SESI SEDANG BERLANGSUNG (OPEN RIGHT NOW!) -> Realtime Widget di Filament
                $session = PresensiSession::updateOrCreate(
                    ['schedule_id' => $schedule->id, 'date' => $todayStr],
                    [
                        'school_id'      => $schedule->school_id,
                        'teacher_id'     => $schedule->teacher_id,
                        'opened_by'      => $schedule->teacher?->user_id,
                        'opened_at'      => now()->subMinutes(20), // Buka 20 menit lalu
                        'start_time'     => now()->subMinutes(20)->toTimeString(),
                        'end_time'       => now()->addMinutes(25)->toTimeString(),
                        'status'         => SessionStatus::Open->value,
                        'material_topic' => 'Sesi LIVE: Praktik Langsung & Diskusi Kelompok',
                        'notes'          => 'Sedang berlangsung, menunggu siswa scan QR.',
                    ]
                );
                $liveOpenCount++;

                // Buat QR Token aktif (berlaku 5 menit ke depan)
                QrToken::create([
                    'presensi_session_id' => $session->id,
                    'token'               => Str::random(40),
                    'expired_at'          => now()->addMinutes(5),
                    'used'                => false,
                ]);

                // Dalam sesi OPEN yang sedang berlangsung: 15 siswa sudah presensi, 5 siswa MASIH BELUM presensi (menunggu di realtime chart!)
                foreach ($students->values() as $sIdx => $student) {
                    if ($sIdx < 15) {
                        // 15 siswa sudah check-in
                        $isLate = ($sIdx === 14); // 1 orang terlambat
                        StudentAttendance::updateOrCreate(
                            ['student_id' => $student->id, 'date' => $todayStr],
                            [
                                'school_id'           => $student->school_id,
                                'class_id'            => $student->class_id,
                                'teacher_id'          => $schedule->teacher_id,
                                'presensi_session_id' => $session->id,
                                'status'              => $isLate ? AttendanceStatus::Late->value : AttendanceStatus::Present->value,
                                'check_in_time'       => now()->subMinutes(rand(1, 18))->toTimeString(),
                                'note'                => $isLate ? 'Terlambat 10 menit' : 'Hadir via Scan QR',
                            ]
                        );
                    } else {
                        // 5 siswa BELUM check in (biarkan kosong agar kelihatan live di dashboard/mobile)
                    }
                }
            } else {
                // 3. SESI SIANG/SORE (SCHEDULED / Belum dibuka)
                // Tidak buat PresensiSession, siap untuk dites buka kelas oleh Guru di mobile/web
            }
        }

        // ─── 4. SEEDING DATABASE NOTIFICATIONS UNTUK NAVBAR FILAMENT ────────
        $this->command->info('🔔 Membuat notifikasi database untuk navbar admin...');
        $admins = \App\Models\User::role(['admin', 'super_admin'])->get();
        if ($admins->isNotEmpty()) {
            \Filament\Notifications\Notification::make()
                ->title('Pengajuan Izin Siswa')
                ->body('Siswa **Citra Izin** (X RPL 1) mengajukan izin untuk hari ini. Menunggu verifikasi!')
                ->warning()
                ->sendToDatabase($admins);

            \Filament\Notifications\Notification::make()
                ->title('Sesi Kelas Dibuka')
                ->body('Guru **Ahmad Fauzi, S.Pd.** telah membuka sesi presensi untuk kelas **X RPL 1 - Matematika**.')
                ->success()
                ->sendToDatabase($admins);

            \Filament\Notifications\Notification::make()
                ->title('Notifikasi WhatsApp Terkirim!')
                ->body('Rekap presensi harian telah berhasil dikirim ke **32 nomor WhatsApp** Orang Tua siswa.')
                ->info()
                ->sendToDatabase($admins);

            \Filament\Notifications\Notification::make()
                ->title('Registrasi Alumni Baru')
                ->body('Alumni **Budi Santoso** (Lulusan 2024) mendaftar ke portal alumni. Menunggu verifikasi.')
                ->info()
                ->sendToDatabase($admins);

            \Filament\Notifications\Notification::make()
                ->title('Lowongan Kerja Baru')
                ->body('PT Telkom Indonesia memposting lowongan kerja **Software Engineer Intern** untuk alumni.')
                ->success()
                ->sendToDatabase($admins);

            \Filament\Notifications\Notification::make()
                ->title('Sesi Kelas Ditutup')
                ->body('Sesi presensi **XII TKJ 1** telah selesai dan ditutup dengan total **28 Hadir, 2 Terlambat**.')
                ->info()
                ->sendToDatabase($admins);
        }

        $this->command->info("⚡ Hari Ini: {$liveOpenCount} Sesi LIVE (OPEN), {$todayClosedCount} Sesi Selesai (CLOSED), sisanya siap dibuka.");
        $this->command->info('🎉 Seeding Presensi Real-Time Selesai Sempurna!');
    }
}
