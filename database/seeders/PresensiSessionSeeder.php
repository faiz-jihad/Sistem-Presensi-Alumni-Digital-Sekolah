<?php

namespace Database\Seeders;

use App\Enums\AttendanceStatus;
use App\Enums\SessionStatus;
use App\Models\ClassHour;
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
        $this->command->info('Seeding contoh sesi presensi...');

        $teacher  = Teacher::first();
        $schedule = Schedule::with(['classHour', 'class'])->where('is_active', true)->first();

        if (!$teacher || !$schedule) {
            $this->command->warn('Tidak ada data guru atau jadwal. Jalankan DummyDataSeeder terlebih dahulu.');
            return;
        }

        $today    = Carbon::today()->toDateString();
        $students = Student::where('class_id', $schedule->class_id)->take(10)->get();

        DB::transaction(function () use ($teacher, $schedule, $today, $students) {

            // ─── Buat sesi untuk hari ini ───────────────────────────────
            $session = PresensiSession::updateOrCreate(
                [
                    'schedule_id' => $schedule->id,
                    'date'        => $today,
                ],
                [
                    'school_id'    => $schedule->school_id,
                    'teacher_id'   => $teacher->id,
                    'opened_by'    => $teacher->user_id,
                    'opened_at'    => Carbon::now()->subMinutes(10),
                    'start_time'   => $schedule->classHour?->start_time ?? '07:00:00',
                    'end_time'     => $schedule->classHour?->end_time ?? '07:45:00',
                    'status'       => SessionStatus::Open->value,
                    'material_topic' => 'Pengenalan Variabel dan Tipe Data',
                    'notes'        => 'Kelas berjalan dengan baik.',
                ]
            );

            $this->command->info("Sesi presensi dibuat: ID {$session->id}");

            // ─── Buat record presensi siswa ─────────────────────────────
            $statuses = [
                AttendanceStatus::Present->value,
                AttendanceStatus::Present->value,
                AttendanceStatus::Present->value,
                AttendanceStatus::Late->value,
                AttendanceStatus::Absent->value,
                AttendanceStatus::Permission->value,
                AttendanceStatus::Sick->value,
                AttendanceStatus::Present->value,
                AttendanceStatus::Present->value,
                AttendanceStatus::Late->value,
            ];

            foreach ($students as $i => $student) {
                $status = $statuses[$i] ?? AttendanceStatus::Present->value;

                StudentAttendance::updateOrCreate(
                    [
                        'student_id'          => $student->id,
                        'presensi_session_id' => $session->id,
                    ],
                    [
                        'school_id'     => $student->school_id,
                        'class_id'      => $student->class_id,
                        'teacher_id'    => $teacher->id,
                        'date'          => $today,
                        'status'        => $status,
                        'check_in_time' => in_array($status, [
                            AttendanceStatus::Present->value,
                            AttendanceStatus::Late->value,
                        ]) ? Carbon::now()->subMinutes(rand(0, 20))->toTimeString() : null,
                        'note' => $status === AttendanceStatus::Late->value
                            ? 'Terlambat karena macet'
                            : ($status === AttendanceStatus::Permission->value
                                ? 'Ada keperluan keluarga'
                                : null),
                    ]
                );
            }

            $this->command->info("Record presensi dibuat untuk {$students->count()} siswa.");

            // ─── Buat QR token contoh ───────────────────────────────────
            $qrToken = QrToken::create([
                'presensi_session_id' => $session->id,
                'token'               => Str::random(40),
                'expired_at'          => Carbon::now()->addMinutes(5),
                'used'                => false,
            ]);

            $this->command->info("QR Token dibuat: {$qrToken->token}");

            // ─── Buat sesi kemarin (sudah closed) ──────────────────────
            $yesterday = Carbon::yesterday()->toDateString();
            $closedSession = PresensiSession::updateOrCreate(
                [
                    'schedule_id' => $schedule->id,
                    'date'        => $yesterday,
                ],
                [
                    'school_id'   => $schedule->school_id,
                    'teacher_id'  => $teacher->id,
                    'opened_by'   => $teacher->user_id,
                    'opened_at'   => Carbon::yesterday()->setTimeFromTimeString('07:00:00'),
                    'start_time'  => '07:00:00',
                    'end_time'    => '07:45:00',
                    'status'      => SessionStatus::Closed->value,
                    'closed_by'   => $teacher->user_id,
                    'closed_at'   => Carbon::yesterday()->setTimeFromTimeString('07:45:00'),
                    'material_topic' => 'Struktur Kontrol: If-Else dan Switch',
                ]
            );

            $this->command->info("Sesi kemarin (closed) dibuat: ID {$closedSession->id}");
        });

        $this->command->info('✅ PresensiSessionSeeder selesai.');
    }
}
