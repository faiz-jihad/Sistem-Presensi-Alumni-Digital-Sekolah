<?php

use App\Models\PrayerAttendance;
use App\Models\School;
use App\Models\Student;
use App\Models\StudentClass;
use App\Models\Teacher;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

beforeEach(function () {
    Carbon::setTestNow(Carbon::parse('2026-07-18 12:15:00', 'Asia/Jakarta'));
    Cache::clear();
    Http::fake([
        'https://equran.id/api/v2/shalat' => Http::response([
            'code' => 200,
            'message' => 'Jadwal shalat berhasil diambil',
            'data' => [
                'provinsi' => 'Jawa Barat',
                'kabkota' => 'Kota Bandung',
                'bulan' => 7,
                'tahun' => 2026,
                'jadwal' => [[
                    'tanggal' => 18,
                    'tanggal_lengkap' => '2026-07-18',
                    'hari' => 'Sabtu',
                    'subuh' => '04:40',
                    'dzuhur' => '12:00',
                    'ashar' => '15:20',
                    'maghrib' => '18:05',
                    'isya' => '19:15',
                ]],
            ],
        ]),
    ]);
});

afterEach(function () {
    Carbon::setTestNow();
});

function makePrayerAttendanceFixture(): array
{
    $school = School::create([
        'name' => 'SMK Presensi Sholat',
        'npsn' => '87654321',
        'prayer_province' => 'Jawa Barat',
        'prayer_city' => 'Kota Bandung',
        'status' => 'active',
    ]);

    $teacherUser = User::create([
        'name' => 'Guru Wali',
        'email' => 'guru.sholat@example.com',
        'password' => 'password123',
        'role' => 'teacher',
        'school_id' => $school->id,
        'status' => 'active',
    ]);
    $teacher = Teacher::create([
        'school_id' => $school->id,
        'user_id' => $teacherUser->id,
        'nip' => '999999999999999999',
        'name' => 'Guru Wali',
        'status' => 'active',
    ]);
    $class = StudentClass::create([
        'school_id' => $school->id,
        'name' => 'XI RPL 1',
        'grade' => '11',
        'major' => 'RPL',
        'homeroom_teacher_id' => $teacher->id,
        'status' => 'active',
    ]);
    $otherClass = StudentClass::create([
        'school_id' => $school->id,
        'name' => 'XI TKJ 1',
        'grade' => '11',
        'major' => 'TKJ',
        'status' => 'active',
    ]);
    $student = Student::create([
        'school_id' => $school->id,
        'class_id' => $class->id,
        'nis' => '260001',
        'nisn' => '0012345678',
        'name' => 'Siswa Sholat',
        'gender' => 'male',
        'birth_date' => '2009-01-01',
        'status' => 'active',
    ]);
    $otherStudent = Student::create([
        'school_id' => $school->id,
        'class_id' => $otherClass->id,
        'nis' => '260002',
        'nisn' => '0012345679',
        'name' => 'Siswa Kelas Lain',
        'gender' => 'female',
        'birth_date' => '2009-02-01',
        'status' => 'active',
    ]);
    $studentUser = User::create([
        'name' => $student->name,
        'email' => 'siswa.sholat@example.com',
        'password' => 'password123',
        'role' => 'student',
        'school_id' => $school->id,
        'status' => 'active',
    ]);

    return compact(
        'school',
        'teacherUser',
        'teacher',
        'class',
        'otherClass',
        'student',
        'otherStudent',
        'studentUser'
    );
}

test('siswa menerima jadwal sholat hari ini dari equran', function () {
    $fixture = makePrayerAttendanceFixture();

    $this->actingAs($fixture['studentUser'], 'sanctum')
        ->getJson('/api/v1/prayer-attendances/today')
        ->assertOk()
        ->assertJsonPath('data.location.city', 'Kota Bandung')
        ->assertJsonPath('data.items.1.prayer_type', 'dzuhur')
        ->assertJsonPath('data.items.1.scheduled_at', '12:00')
        ->assertJsonPath('data.items.1.status', 'open')
        ->assertJsonPath('data.items.1.can_submit', true);

    Http::assertSent(fn ($request) => $request->url() === 'https://equran.id/api/v2/shalat' &&
        $request['provinsi'] === 'Jawa Barat' &&
        $request['kabkota'] === 'Kota Bandung' &&
        $request['bulan'] === 7 &&
        $request['tahun'] === 2026
    );
});

test('siswa hanya dapat mengirim satu presensi untuk sholat dan tanggal yang sama', function () {
    $fixture = makePrayerAttendanceFixture();

    $this->actingAs($fixture['studentUser'], 'sanctum')
        ->postJson('/api/v1/prayer-attendances', ['prayer_type' => 'dzuhur'])
        ->assertCreated()
        ->assertJsonPath('data.status', 'pending')
        ->assertJsonPath('data.student_id', $fixture['student']->id);

    $this->actingAs($fixture['studentUser'], 'sanctum')
        ->postJson('/api/v1/prayer-attendances', ['prayer_type' => 'dzuhur'])
        ->assertStatus(422)
        ->assertJsonPath('message', 'Anda sudah mengirim presensi sholat ini.');

    expect(PrayerAttendance::count())->toBe(1);
});

test('guru hanya melihat dan memverifikasi presensi siswa kelas wali', function () {
    $fixture = makePrayerAttendanceFixture();

    $allowed = PrayerAttendance::create([
        'school_id' => $fixture['school']->id,
        'class_id' => $fixture['class']->id,
        'student_id' => $fixture['student']->id,
        'prayer_type' => 'dzuhur',
        'attendance_date' => now()->toDateString(),
        'scheduled_at' => '12:00:00',
        'submitted_at' => now(),
        'status' => 'pending',
    ]);
    PrayerAttendance::create([
        'school_id' => $fixture['school']->id,
        'class_id' => $fixture['otherClass']->id,
        'student_id' => $fixture['otherStudent']->id,
        'prayer_type' => 'dzuhur',
        'attendance_date' => now()->toDateString(),
        'scheduled_at' => '12:00:00',
        'submitted_at' => now(),
        'status' => 'pending',
    ]);

    $this->actingAs($fixture['teacherUser'], 'sanctum')
        ->getJson('/api/v1/prayer-attendances/pending')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.student_id', $fixture['student']->id);

    $this->actingAs($fixture['teacherUser'], 'sanctum')
        ->postJson("/api/v1/prayer-attendances/{$allowed->id}/verify", [
            'approved' => true,
            'note' => 'Sudah dikonfirmasi.',
        ])
        ->assertOk()
        ->assertJsonPath('data.status', 'approved')
        ->assertJsonPath('data.verifier_name', $fixture['teacherUser']->name);

    $this->assertDatabaseHas('prayer_attendances', [
        'id' => $allowed->id,
        'status' => 'approved',
        'verified_by' => $fixture['teacherUser']->id,
        'teacher_note' => 'Sudah dikonfirmasi.',
    ]);
});
