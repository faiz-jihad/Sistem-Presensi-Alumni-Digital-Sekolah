<?php

use App\Models\AcademicYear;
use App\Models\ClassHour;
use App\Models\PresensiSession;
use App\Models\School;
use App\Models\Schedule;
use App\Models\SchoolClass;
use App\Models\Semester;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use App\Services\AttendanceService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    Carbon::setTestNow(Carbon::parse('2026-07-04 07:05:00'));
});

afterEach(function () {
    Carbon::setTestNow();
});

function makeQrAttendanceFixture(array $sessionOverrides = []): array
{
    $school = School::create([
        'name' => 'SMK Example',
        'npsn' => '12345678',
    ]);

    $teacherUser = User::create([
        'name' => 'Guru Test',
        'email' => 'guru.test@example.com',
        'password' => 'password123',
        'role' => 'teacher',
        'school_id' => $school->id,
        'status' => 'active',
    ]);

    $teacher = Teacher::create([
        'school_id' => $school->id,
        'user_id' => $teacherUser->id,
        'nip' => '123456789012345678',
        'name' => 'Guru Test',
        'employment_status' => 'honorer',
        'status' => 'active',
    ]);

    $academicYear = AcademicYear::create([
        'school_id' => $school->id,
        'name' => '2026/2027',
        'start_year' => 2026,
        'end_year' => 2027,
        'start_date' => '2026-07-01',
        'end_date' => '2027-06-30',
        'is_active' => true,
    ]);

    $semester = Semester::create([
        'academic_year_id' => $academicYear->id,
        'type' => 'odd',
        'name' => 'Semester Ganjil 2026/2027',
        'start_date' => '2026-07-01',
        'end_date' => '2026-12-31',
        'is_active' => true,
    ]);

    $classHour = ClassHour::create([
        'school_id' => $school->id,
        'code' => 'J1',
        'start_time' => '07:00:00',
        'end_time' => '08:00:00',
        'duration_minutes' => 60,
        'order' => 1,
        'is_break' => false,
        'shift' => 'morning',
        'status' => 'active',
    ]);

    $subject = Subject::create([
        'school_id' => $school->id,
        'code' => 'MTK01',
        'name' => 'Matematika',
        'group' => 'general',
        'credit_hours' => 2,
        'status' => 'active',
    ]);

    $class = SchoolClass::create([
        'school_id' => $school->id,
        'academic_year_id' => $academicYear->id,
        'name' => 'XII RPL 1',
        'grade' => '12',
        'major' => 'RPL',
        'status' => 'active',
    ]);

    $schedule = Schedule::create([
        'school_id' => $school->id,
        'class_id' => $class->id,
        'subject_id' => $subject->id,
        'teacher_id' => $teacher->id,
        'opened_by' => $teacherUser->id,
        'class_hour_id' => $classHour->id,
        'semester_id' => $semester->id,
        'day' => 'saturday',
        'room' => 'A1',
        'is_active' => true,
    ]);

    $session = PresensiSession::create(array_merge([
        'school_id' => $school->id,
        'schedule_id' => $schedule->id,
        'teacher_id' => $teacher->id,
        'date' => now()->toDateString(),
        'start_time' => now()->subMinutes(5)->format('H:i:s'),
        'end_time' => now()->addMinutes(55)->format('H:i:s'),
        'status' => 'open',
    ], $sessionOverrides));

    $student = Student::create([
        'school_id' => $school->id,
        'class_id' => $class->id,
        'nis' => '1001',
        'nisn' => '1001001',
        'name' => 'Arif',
        'gender' => 'male',
        'birth_date' => '2010-01-01',
        'status' => 'active',
    ]);

    return [$session, $student, $teacherUser];
}

test('qr attendance can be recorded once per student per day', function () {
    [$session, $student] = makeQrAttendanceFixture();

    $service = app(AttendanceService::class);

    $first = $service->recordSelfPresence($student->id, 'session_' . $session->id);

    expect($first->id)->not->toBeNull()
        ->and($first->presensi_session_id)->toBe($session->id)
        ->and(StudentAttendance::where('student_id', $student->id)->count())->toBe(1);

    expect(fn () => $service->recordSelfPresence($student->id, 'session_' . $session->id))
        ->toThrow(Exception::class, 'Anda sudah melakukan presensi');

    expect(StudentAttendance::where('student_id', $student->id)->count())->toBe(1);
});

test('qr attendance is rejected after the session end time', function () {
    Carbon::setTestNow(Carbon::parse('2026-07-04 09:01:00'));

    [$session, $student] = makeQrAttendanceFixture([
        'start_time' => '07:00:00',
        'end_time' => '09:00:00',
        'status' => 'open',
    ]);

    $service = app(AttendanceService::class);

    expect(fn () => $service->recordSelfPresence($student->id, 'session_' . $session->id))
        ->toThrow(Exception::class, 'Sesi presensi ini sudah berakhir.');

    expect(StudentAttendance::where('student_id', $student->id)->count())->toBe(0);
});

test('qr attendance stores teacher notification without queue worker', function () {
    [$session, $student, $teacherUser] = makeQrAttendanceFixture();

    app(AttendanceService::class)
        ->recordSelfPresence($student->id, 'session_' . $session->id);

    $notification = $teacherUser->notifications()
        ->get()
        ->first(fn ($item) => ($item->data['title'] ?? null) === 'Siswa Scan QR');

    expect($notification)->not->toBeNull()
        ->and($notification->data['title'] ?? null)->toBe('Siswa Scan QR')
        ->and($notification->data['data']['student_id'] ?? null)->toBe($student->id);
});
