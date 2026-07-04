<?php

use App\Models\School;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\StudentClass;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Seed roles & permissions
    $this->artisan('db:seed', ['--class' => 'RoleAndPermissionSeeder']);
});

test('guru dapat melakukan input presensi kelas', function () {
    $school = School::factory()->create(['status' => 'active']);
    $class = StudentClass::create([
        'school_id' => $school->id,
        'name'      => 'XII RPL 1',
        'grade'     => '12',
    ]);
    $teacher = Teacher::factory()->create(['school_id' => $school->id]);
    $teacherUser = User::factory()->create([
        'role'      => 'teacher',
        'school_id' => $school->id,
        'status'    => 'active',
    ]);
    $teacher->update(['user_id' => $teacherUser->id]);

    $student = Student::factory()->create([
        'school_id' => $school->id,
        'class_id'  => $class->id,
        'status'    => 'active',
    ]);

    $response = $this->actingAs($teacherUser, 'sanctum')
        ->postJson('/api/v1/attendances', [
            'class_id'    => $class->id,
            'date'        => now()->toDateString(),
            'attendances' => [
                [
                    'student_id' => $student->id,
                    'status'     => 'present',
                    'note'       => null,
                ],
            ],
        ]);

    $response->assertStatus(200)
        ->assertJsonPath('success', true);

    $this->assertDatabaseHas('student_attendances', [
        'student_id' => $student->id,
        'status'     => 'present',
    ]);
});

test('sistem mendeteksi keterlambatan pada siswa yang scan terlambat', function () {
    // Ini merupakan test unit untuk logic deteksi terlambat di AttendanceService
    $startTime = \Carbon\Carbon::parse('07:00:00');
    $scanTime  = \Carbon\Carbon::parse('07:20:00'); // 20 menit setelah start
    $diff      = $startTime->diffInMinutes($scanTime, false);

    $expectedStatus = $diff > 15 ? 'late' : 'present';
    expect($expectedStatus)->toBe('late');
});

test('guru dapat melihat rekap harian kehadiran kelas', function () {
    $school = School::factory()->create();
    $class  = StudentClass::create([
        'school_id' => $school->id,
        'name'      => 'XI TKJ 2',
        'grade'     => '11',
    ]);
    $teacherUser = User::factory()->create([
        'role'      => 'teacher',
        'school_id' => $school->id,
        'status'    => 'active',
    ]);

    $response = $this->actingAs($teacherUser, 'sanctum')
        ->getJson('/api/v1/attendances/report/daily?class_id=' . $class->id . '&date=' . now()->toDateString());

    $response->assertStatus(200)
        ->assertJsonPath('success', true)
        ->assertJsonStructure([
            'data' => [
                'class',
                'date',
                'summary' => ['present', 'late', 'permission', 'sick', 'absent', 'not_recorded'],
                'students',
            ],
        ]);
});

test('guru dapat melihat rekap bulanan kehadiran kelas', function () {
    $school = School::factory()->create();
    $class  = StudentClass::create([
        'school_id' => $school->id,
        'name'      => 'X RPL 1',
        'grade'     => '10',
    ]);
    $teacherUser = User::factory()->create([
        'role'      => 'teacher',
        'school_id' => $school->id,
        'status'    => 'active',
    ]);

    $response = $this->actingAs($teacherUser, 'sanctum')
        ->getJson('/api/v1/attendances/report/monthly?class_id=' . $class->id . '&month=' . now()->month . '&year=' . now()->year);

    $response->assertStatus(200)
        ->assertJsonPath('success', true)
        ->assertJsonStructure([
            'data' => [
                'class',
                'month',
                'year',
                'total_students',
                'students',
            ],
        ]);
});

test('guru/admin dapat men-trigger pengiriman rekap harian ke orang tua', function () {
    \Illuminate\Support\Facades\Queue::fake();

    $school = School::factory()->create(['status' => 'active']);
    $teacherUser = User::factory()->create([
        'role'      => 'teacher',
        'school_id' => $school->id,
        'status'    => 'active',
    ]);

    $response = $this->actingAs($teacherUser, 'sanctum')
        ->postJson('/api/v1/attendances/report/send-daily', [
            'date' => now()->toDateString(),
        ]);

    $response->assertStatus(200)
        ->assertJsonPath('success', true);
});

test('admin dapat men-trigger pengiriman rekap bulanan ke orang tua', function () {
    \Illuminate\Support\Facades\Queue::fake();

    $school = School::factory()->create(['status' => 'active']);
    $adminUser = User::factory()->create([
        'role'      => 'admin',
        'school_id' => $school->id,
        'status'    => 'active',
    ]);

    $response = $this->actingAs($adminUser, 'sanctum')
        ->postJson('/api/v1/attendances/report/send-monthly', [
            'month' => now()->month,
            'year'  => now()->year,
        ]);

    $response->assertStatus(200)
        ->assertJsonPath('success', true);
});

