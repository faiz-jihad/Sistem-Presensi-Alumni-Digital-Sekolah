<?php

use App\Models\School;
use App\Models\StudentClass;
use App\Models\Teacher;
use App\Models\User;
use App\Models\ClassHour;
use App\Models\Semester;
use App\Models\Schedule;
use App\Models\Subject;
use App\Models\AcademicYear;
use App\Enums\DayOfWeek;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Notifications\DatabaseNotification;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Seed roles & permissions
    $this->artisan('db:seed', ['--class' => 'RoleAndPermissionSeeder']);
});

test('kirim pengingat jadwal mengajar ke guru 15 menit sebelum kelas dimulai', function () {
    // Set waktu pengujian ke hari Senin, 13 Juli 2026 pukul 07:45:00
    $testNow = Carbon::parse('2026-07-13 07:45:00'); 
    Carbon::setTestNow($testNow);

    $school = School::create([
        'name'   => 'SMK Negeri 1 Jakarta',
        'npsn'   => '12345678',
        'status' => 'active',
    ]);
    
    $class = StudentClass::create([
        'school_id' => $school->id,
        'name'      => 'XII RPL 1',
        'grade'     => '12',
    ]);
    
    $teacherUser = User::create([
        'name'      => 'Budi Guru',
        'email'     => 'budiguru@example.com',
        'password'  => bcrypt('password'),
        'role'      => 'teacher',
        'school_id' => $school->id,
        'status'    => 'active',
    ]);

    $teacher = Teacher::create([
        'school_id' => $school->id,
        'user_id'   => $teacherUser->id,
        'nip'       => '198701022010011002',
        'name'      => 'Budi Guru',
        'gender'    => 'male',
        'status'    => 'active',
    ]);

    $academicYear = AcademicYear::create([
        'school_id'  => $school->id,
        'name'       => '2026/2027',
        'start_year' => 2026,
        'end_year'   => 2027,
        'start_date' => '2026-07-01',
        'end_date'   => '2027-06-30',
        'is_active'  => true,
    ]);

    $semester = Semester::create([
        'academic_year_id' => $academicYear->id,
        'type'             => 'odd',
        'name'             => 'Semester Ganjil 2026/2027',
        'start_date'       => '2026-07-01',
        'end_date'         => '2026-12-31',
        'is_active'        => true,
    ]);

    // Jam pelajaran mulai 15 menit lagi (08:00:00)
    $classHour = ClassHour::create([
        'school_id' => $school->id,
        'code' => 'J1',
        'start_time' => '08:00:00',
        'end_time' => '08:45:00',
        'duration_minutes' => 45,
        'order' => 1,
    ]);

    $subject = Subject::create([
        'school_id' => $school->id,
        'name'      => 'Pemrograman Web',
        'code'      => 'PW01',
    ]);

    $schedule = Schedule::create([
        'school_id' => $school->id,
        'class_id' => $class->id,
        'subject_id' => $subject->id,
        'teacher_id' => $teacher->id,
        'class_hour_id' => $classHour->id,
        'semester_id' => $semester->id,
        'day' => DayOfWeek::Monday,
        'is_active' => true,
    ]);

    // Jalankan perintah Artisan
    Artisan::call('app:send-teacher-schedule-reminder');

    // Filter notifikasi pengingat secara spesifik (menghindari collision dengan notifikasi model observer)
    $notifications = DatabaseNotification::where('notifiable_id', $teacherUser->id)->get();
    $reminder = $notifications->first(fn ($n) => ($n->data['title'] ?? '') === 'Pengingat Jadwal Mengajar 🗓️');

    expect($reminder)->not->toBeNull();
    expect($reminder->data['body'])->toContain('XII RPL 1');
    expect($reminder->data['body'])->toContain('08:00');

    Carbon::setTestNow();
});

test('tidak mengirim pengingat jika kelas dimulai lebih dari 15 menit', function () {
    // Set waktu pengujian ke hari Senin, 13 Juli 2026 pukul 07:30:00 (kelas dimulai 30 menit lagi)
    $testNow = Carbon::parse('2026-07-13 07:30:00'); 
    Carbon::setTestNow($testNow);

    $school = School::create([
        'name'   => 'SMK Negeri 1 Jakarta',
        'npsn'   => '12345678',
        'status' => 'active',
    ]);
    
    $class = StudentClass::create([
        'school_id' => $school->id,
        'name'      => 'XII RPL 1',
        'grade'     => '12',
    ]);
    
    $teacherUser = User::create([
        'name'      => 'Budi Guru',
        'email'     => 'budiguru@example.com',
        'password'  => bcrypt('password'),
        'role'      => 'teacher',
        'school_id' => $school->id,
        'status'    => 'active',
    ]);

    $teacher = Teacher::create([
        'school_id' => $school->id,
        'user_id'   => $teacherUser->id,
        'nip'       => '198701022010011002',
        'name'      => 'Budi Guru',
        'gender'    => 'male',
        'status'    => 'active',
    ]);

    $academicYear = AcademicYear::create([
        'school_id'  => $school->id,
        'name'       => '2026/2027',
        'start_year' => 2026,
        'end_year'   => 2027,
        'start_date' => '2026-07-01',
        'end_date'   => '2027-06-30',
        'is_active'  => true,
    ]);

    $semester = Semester::create([
        'academic_year_id' => $academicYear->id,
        'type'             => 'odd',
        'name'             => 'Semester Ganjil 2026/2027',
        'start_date'       => '2026-07-01',
        'end_date'         => '2026-12-31',
        'is_active'        => true,
    ]);

    $classHour = ClassHour::create([
        'school_id' => $school->id,
        'code' => 'J1',
        'start_time' => '08:00:00',
        'end_time' => '08:45:00',
        'duration_minutes' => 45,
        'order' => 1,
    ]);

    $subject = Subject::create([
        'school_id' => $school->id,
        'name'      => 'Pemrograman Web',
        'code'      => 'PW01',
    ]);

    $schedule = Schedule::create([
        'school_id' => $school->id,
        'class_id' => $class->id,
        'subject_id' => $subject->id,
        'teacher_id' => $teacher->id,
        'class_hour_id' => $classHour->id,
        'semester_id' => $semester->id,
        'day' => DayOfWeek::Monday,
        'is_active' => true,
    ]);

    Artisan::call('app:send-teacher-schedule-reminder');

    // Pastikan notifikasi dengan title "Pengingat Jadwal Mengajar 🗓️" TIDAK terkirim
    $notifications = DatabaseNotification::where('notifiable_id', $teacherUser->id)->get();
    $reminder = $notifications->first(fn ($n) => ($n->data['title'] ?? '') === 'Pengingat Jadwal Mengajar 🗓️');

    expect($reminder)->toBeNull();

    Carbon::setTestNow();
});
