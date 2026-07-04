<?php

use App\Models\School;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\StudentClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'RoleAndPermissionSeeder']);
});

test('siswa dapat mengajukan izin', function () {
    $school     = School::factory()->create(['status' => 'active']);
    $class      = StudentClass::create([
        'school_id' => $school->id,
        'name'      => 'XII RPL 1',
        'grade'     => '12',
    ]);
    $studentUser = User::factory()->create([
        'role'   => 'student',
        'status' => 'active',
    ]);
    $student = Student::factory()->create([
        'school_id' => $school->id,
        'class_id'  => $class->id,
        'status'    => 'active',
        'name'      => $studentUser->name,
    ]);

    $response = $this->actingAs($studentUser, 'sanctum')
        ->postJson('/api/v1/attendances/izin', [
            'date'   => now()->toDateString(),
            'status' => 'permission',
            'note'   => 'Ada keperluan keluarga',
        ]);

    $response->assertStatus(200)
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.status', 'permission')
        ->assertJsonPath('data.verification_status', 'pending');
});

test('siswa dapat mengajukan sakit', function () {
    $school     = School::factory()->create(['status' => 'active']);
    $class      = StudentClass::create([
        'school_id' => $school->id,
        'name'      => 'XI TKJ 1',
        'grade'     => '11',
    ]);
    $studentUser = User::factory()->create([
        'role'   => 'student',
        'status' => 'active',
    ]);
    $student = Student::factory()->create([
        'school_id' => $school->id,
        'class_id'  => $class->id,
        'status'    => 'active',
        'name'      => $studentUser->name,
    ]);

    $response = $this->actingAs($studentUser, 'sanctum')
        ->postJson('/api/v1/attendances/izin', [
            'date'   => now()->toDateString(),
            'status' => 'sick',
            'note'   => 'Demam sejak kemarin',
        ]);

    $response->assertStatus(200)
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.status', 'sick');
});

test('admin dapat menyetujui pengajuan izin siswa', function () {
    $school     = School::factory()->create(['status' => 'active']);
    $class      = StudentClass::create([
        'school_id' => $school->id,
        'name'      => 'X RPL 1',
        'grade'     => '10',
    ]);
    $student    = Student::factory()->create([
        'school_id' => $school->id,
        'class_id'  => $class->id,
        'status'    => 'active',
    ]);
    $attendance = StudentAttendance::create([
        'school_id'           => $school->id,
        'class_id'            => $class->id,
        'student_id'          => $student->id,
        'date'                => now()->toDateString(),
        'status'              => 'permission',
        'note'                => 'Izin keperluan keluarga',
        'verification_status' => 'pending',
    ]);

    $adminUser = User::factory()->create([
        'role'      => 'admin',
        'school_id' => $school->id,
        'status'    => 'active',
    ]);

    $response = $this->actingAs($adminUser, 'sanctum')
        ->postJson("/api/v1/attendances/{$attendance->id}/verify", [
            'verification_status' => 'approved',
        ]);

    $response->assertStatus(200)
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.verification_status', 'approved');

    $this->assertDatabaseHas('student_attendances', [
        'id'                  => $attendance->id,
        'verification_status' => 'approved',
    ]);
});

test('admin dapat menolak pengajuan izin dan status berubah menjadi alpha', function () {
    $school     = School::factory()->create(['status' => 'active']);
    $class      = StudentClass::create([
        'school_id' => $school->id,
        'name'      => 'X RPL 2',
        'grade'     => '10',
    ]);
    $student    = Student::factory()->create([
        'school_id' => $school->id,
        'class_id'  => $class->id,
        'status'    => 'active',
    ]);
    $attendance = StudentAttendance::create([
        'school_id'           => $school->id,
        'class_id'            => $class->id,
        'student_id'          => $student->id,
        'date'                => now()->toDateString(),
        'status'              => 'sick',
        'note'                => 'Sakit',
        'verification_status' => 'pending',
    ]);

    $adminUser = User::factory()->create([
        'role'      => 'admin',
        'school_id' => $school->id,
        'status'    => 'active',
    ]);

    $response = $this->actingAs($adminUser, 'sanctum')
        ->postJson("/api/v1/attendances/{$attendance->id}/verify", [
            'verification_status' => 'rejected',
        ]);

    $response->assertStatus(200)
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.verification_status', 'rejected');

    // Status diubah ke absent saat ditolak
    $this->assertDatabaseHas('student_attendances', [
        'id'                  => $attendance->id,
        'status'              => 'absent',
        'verification_status' => 'rejected',
    ]);
});

test('validasi wajib saat mengajukan izin tanpa note', function () {
    $studentUser = User::factory()->create(['role' => 'student', 'status' => 'active']);

    $response = $this->actingAs($studentUser, 'sanctum')
        ->postJson('/api/v1/attendances/izin', [
            'date'   => now()->toDateString(),
            'status' => 'permission',
            // note tidak disertakan
        ]);

    $response->assertStatus(422);
});
