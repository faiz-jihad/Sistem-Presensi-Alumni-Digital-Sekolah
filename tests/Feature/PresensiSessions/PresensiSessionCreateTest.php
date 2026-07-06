<?php

use App\Models\AcademicYear;
use App\Models\ClassHour;
use App\Models\PresensiSession;
use App\Models\School;
use App\Models\Schedule;
use App\Models\SchoolClass;
use App\Models\Semester;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('presensi session creation succeeds when a schedule is provided', function () {
    $school = School::create([
        'name' => 'SMK Example',
        'npsn' => '12345678',
    ]);

    $teacher = Teacher::create([
        'school_id' => $school->id,
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
        'class_hour_id' => $classHour->id,
        'semester_id' => $semester->id,
        'day' => 'monday',
        'room' => 'A1',
        'is_active' => true,
    ]);

    $session = PresensiSession::create([
        'school_id' => $school->id,
        'schedule_id' => $schedule->id,
        'teacher_id' => $teacher->id,
        'date' => now()->toDateString(),
        'status' => 'scheduled',
    ]);

    expect($session->exists)->toBeTrue()
        ->and($session->schedule_id)->toBe($schedule->id);
});
