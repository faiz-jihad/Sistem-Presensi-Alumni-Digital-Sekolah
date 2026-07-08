<?php

namespace Tests\Feature;

use App\Enums\AttendanceStatus;
use App\Enums\SessionStatus;
use App\Models\AcademicYear;
use App\Models\ClassHour;
use App\Models\PresensiSession;
use App\Models\QrToken;
use App\Models\Schedule;
use App\Models\School;
use App\Models\Semester;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\StudentClass;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class AttendanceFlowTest extends TestCase
{
    use RefreshDatabase;

    private User $teacherUser;
    private Teacher $teacher;
    private Schedule $schedule;
    private StudentClass $class;
    private Student $student1;
    private Student $student2;
    private School $school;
    private ClassHour $classHour;
    private Subject $subject;
    private Semester $semester;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedDatabase();
    }

    private function seedDatabase(): void
    {
        // Seed Spatie roles & permissions via seeder
        $this->artisan('db:seed', ['--class' => 'RoleAndPermissionSeeder']);

        // ─── School ────────────────────────────────────────────
        $this->school = School::create([
            'name'  => 'SMK Test',
            'npsn'  => '12345678',
            'level' => 'smk',
        ]);

        // ─── Academic Year ────────────────────────────────────
        $academicYear = AcademicYear::create([
            'school_id'  => $this->school->id,
            'name'       => '2024/2025',
            'start_year' => 2024,
            'end_year'   => 2025,
            'start_date' => '2024-07-01',
            'end_date'   => '2025-06-30',
            'is_active'  => true,
        ]);

        // ─── Semester ─────────────────────────────────────────
        $this->semester = Semester::create([
            'academic_year_id' => $academicYear->id,
            'type'             => 'odd',
            'name'             => 'Semester Ganjil 2024/2025',
            'start_date'       => '2024-07-01',
            'end_date'         => '2024-12-31',
            'is_active'        => true,
        ]);

        // ─── Subject ──────────────────────────────────────────
        $this->subject = Subject::create([
            'school_id' => $this->school->id,
            'code'      => 'MTK',
            'name'      => 'Matematika',
        ]);

        // ─── ClassHour ────────────────────────────────────────
        $this->classHour = ClassHour::create([
            'school_id'        => $this->school->id,
            'code'             => 'JP1',
            'start_time'       => Carbon::now()->subMinute()->format('H:i:s'),
            'end_time'         => Carbon::now()->addHour()->format('H:i:s'),
            'duration_minutes' => 45,
            'order'            => 1,
        ]);

        // ─── Class ────────────────────────────────────────────
        $this->class = StudentClass::create([
            'school_id' => $this->school->id,
            'name'      => 'X RPL 1',
            'grade'     => '10',
        ]);

        // ─── Teacher User ─────────────────────────────────────
        $this->teacherUser = User::create([
            'name'      => 'Guru Test',
            'email'     => 'guru@test.com',
            'password'  => bcrypt('password'),
            'role'      => 'teacher',
            'school_id' => $this->school->id,
        ]);

        $this->teacher = Teacher::create([
            'school_id' => $this->school->id,
            'user_id'   => $this->teacherUser->id,
            'name'      => 'Guru Test',
            'nip'       => '123456789012345678',
            'status'    => 'active',
        ]);

        $this->class->update([
            'homeroom_teacher_id' => $this->teacher->id,
        ]);

        // ─── Schedule (hari ini) ──────────────────────────────
        $todayDay      = strtolower(Carbon::now()->format('l'));
        $this->schedule = Schedule::create([
            'school_id'        => $this->school->id,
            'class_id'         => $this->class->id,
            'subject_id'       => $this->subject->id,
            'teacher_id'       => $this->teacher->id,
            'class_hour_id'    => $this->classHour->id,
            'semester_id'      => $this->semester->id,
            'day'              => $todayDay,
            'is_active'        => true,
            'allow_early_open' => false,
        ]);

        // ─── Students ─────────────────────────────────────────
        $this->student1 = Student::create([
            'school_id'  => $this->school->id,
            'class_id'   => $this->class->id,
            'nis'        => '2024001',
            'nisn'       => '0001234567',
            'name'       => 'Siswa Satu',
            'gender'     => 'male',
            'birth_date' => '2008-01-01',
            'status'     => 'active',
        ]);

        $this->student2 = Student::create([
            'school_id'  => $this->school->id,
            'class_id'   => $this->class->id,
            'nis'        => '2024002',
            'nisn'       => '0001234568',
            'name'       => 'Siswa Dua',
            'gender'     => 'female',
            'birth_date' => '2008-02-01',
            'status'     => 'active',
        ]);
    }

    /* ═══════════════════════════════════════════════════════════
     *  Helper: buat open session
     * ═══════════════════════════════════════════════════════════ */
    private function makeOpenSession(): PresensiSession
    {
        return PresensiSession::create([
            'school_id'   => $this->school->id,
            'schedule_id' => $this->schedule->id,
            'teacher_id'  => $this->teacher->id,
            'opened_by'   => $this->teacherUser->id,
            'opened_at'   => now(),
            'date'        => Carbon::today()->toDateString(),
            'start_time'  => $this->classHour->start_time,
            'end_time'    => $this->classHour->end_time,
            'status'      => SessionStatus::Open->value,
        ]);
    }

    /* ═══════════════════════════════════════════════════════════
     *  TEST 1: GET /teacher/today
     * ═══════════════════════════════════════════════════════════ */
    public function test_guru_dapat_melihat_jadwal_hari_ini(): void
    {
        $response = $this->actingAs($this->teacherUser, 'sanctum')
            ->getJson('/api/v1/teacher/today');

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'data' => [
                    'date',
                    'teacher' => ['id', 'name'],
                    'schedules',
                    'total',
                ],
            ]);

        $schedules = $response->json('data.schedules');
        $this->assertNotEmpty($schedules);
    }

    /* ═══════════════════════════════════════════════════════════
     *  TEST 2: POST /attendance/open
     * ═══════════════════════════════════════════════════════════ */
    public function test_guru_dapat_membuka_kelas(): void
    {
        $response = $this->actingAs($this->teacherUser, 'sanctum')
            ->postJson('/api/v1/attendance/open', [
                'schedule_id' => $this->schedule->id,
            ]);

        $response->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.status', SessionStatus::Open->value);

        $this->assertDatabaseHas('presensi_sessions', [
            'schedule_id' => $this->schedule->id,
            'status'      => SessionStatus::Open->value,
            'opened_by'   => $this->teacherUser->id,
        ]);
    }

    /* ═══════════════════════════════════════════════════════════
     *  TEST 3: Tidak bisa buka dua kali
     * ═══════════════════════════════════════════════════════════ */
    public function test_guru_dapat_melanjutkan_sesi_schedule_yang_sudah_open(): void
    {
        // Buka pertama kali
        $first = $this->actingAs($this->teacherUser, 'sanctum')
            ->postJson('/api/v1/attendance/open', ['schedule_id' => $this->schedule->id])
            ->assertCreated();

        // Coba buka lagi
        $second = $this->actingAs($this->teacherUser, 'sanctum')
            ->postJson('/api/v1/attendance/open', ['schedule_id' => $this->schedule->id])
            ->assertCreated();

        $this->assertSame($first->json('data.id'), $second->json('data.id'));
    }

    public function test_guru_dapat_melanjutkan_sesi_qr_class_based_yang_sudah_open(): void
    {
        $payload = [
            'class_id' => $this->class->id,
            'date' => Carbon::today()->toDateString(),
        ];

        $first = $this->actingAs($this->teacherUser, 'sanctum')
            ->postJson('/api/v1/attendance/open', $payload)
            ->assertCreated();

        $second = $this->actingAs($this->teacherUser, 'sanctum')
            ->postJson('/api/v1/attendance/open', $payload)
            ->assertCreated();

        $this->assertSame($first->json('data.id'), $second->json('data.id'));
        $this->assertDatabaseCount('presensi_sessions', 1);
    }

    /* ═══════════════════════════════════════════════════════════
     *  TEST 4: POST /attendance/manual
     * ═══════════════════════════════════════════════════════════ */
    public function test_guru_dapat_input_presensi_manual(): void
    {
        $session = $this->makeOpenSession();

        $response = $this->actingAs($this->teacherUser, 'sanctum')
            ->postJson('/api/v1/attendance/manual', [
                'session_id'  => $session->id,
                'attendances' => [
                    ['student_id' => $this->student1->id, 'status' => AttendanceStatus::Present->value],
                    ['student_id' => $this->student2->id, 'status' => AttendanceStatus::Late->value, 'note' => 'Terlambat'],
                ],
            ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.count', 2);

        $this->assertDatabaseHas('student_attendances', [
            'student_id'          => $this->student1->id,
            'presensi_session_id' => $session->id,
            'status'              => AttendanceStatus::Present->value,
        ]);

        $this->assertDatabaseHas('student_attendances', [
            'student_id'          => $this->student2->id,
            'presensi_session_id' => $session->id,
            'status'              => AttendanceStatus::Late->value,
        ]);
    }

    /* ═══════════════════════════════════════════════════════════
     *  TEST 5: POST /attendance/generate-qr
     * ═══════════════════════════════════════════════════════════ */
    public function test_presensi_manual_tidak_mengubah_check_in_time_siswa_lain(): void
    {
        $session = $this->makeOpenSession();

        StudentAttendance::create([
            'school_id' => $this->school->id,
            'class_id' => $this->class->id,
            'student_id' => $this->student1->id,
            'teacher_id' => $this->teacher->id,
            'presensi_session_id' => $session->id,
            'date' => $session->date,
            'status' => AttendanceStatus::Present->value,
            'check_in_time' => '07:01:00',
        ]);

        StudentAttendance::create([
            'school_id' => $this->school->id,
            'class_id' => $this->class->id,
            'student_id' => $this->student2->id,
            'teacher_id' => $this->teacher->id,
            'presensi_session_id' => $session->id,
            'date' => $session->date,
            'status' => AttendanceStatus::Present->value,
            'check_in_time' => '07:02:00',
        ]);

        $this->actingAs($this->teacherUser, 'sanctum')
            ->postJson('/api/v1/attendance/manual', [
                'session_id' => $session->id,
                'attendances' => [
                    ['student_id' => $this->student1->id, 'status' => AttendanceStatus::Late->value],
                    ['student_id' => $this->student2->id, 'status' => AttendanceStatus::Present->value],
                ],
            ])
            ->assertOk();

        $this->assertDatabaseHas('student_attendances', [
            'student_id' => $this->student1->id,
            'presensi_session_id' => $session->id,
            'status' => AttendanceStatus::Late->value,
            'check_in_time' => '07:01:00',
        ]);

        $this->assertDatabaseHas('student_attendances', [
            'student_id' => $this->student2->id,
            'presensi_session_id' => $session->id,
            'status' => AttendanceStatus::Present->value,
            'check_in_time' => '07:02:00',
        ]);
    }

    public function test_qr_ditolak_jika_siswa_sudah_dipresensi_manual(): void
    {
        $session = $this->makeOpenSession();

        $generateResponse = $this->actingAs($this->teacherUser, 'sanctum')
            ->postJson('/api/v1/attendance/generate-qr', [
                'session_id' => $session->id,
            ])
            ->assertCreated();

        $this->actingAs($this->teacherUser, 'sanctum')
            ->postJson('/api/v1/attendance/manual', [
                'session_id' => $session->id,
                'attendances' => [
                    ['student_id' => $this->student1->id, 'status' => AttendanceStatus::Present->value],
                ],
            ])
            ->assertOk();

        $studentUser = User::create([
            'name' => $this->student1->name,
            'email' => 'siswa.manual@test.com',
            'password' => bcrypt('password'),
            'role' => 'student',
            'school_id' => $this->school->id,
        ]);

        $this->actingAs($studentUser, 'sanctum')
            ->postJson('/api/v1/attendance/scan', [
                'session_id' => $session->id,
                'token' => $generateResponse->json('data.token'),
            ])
            ->assertStatus(422)
            ->assertJsonPath('message', 'Anda sudah melakukan presensi.');

        $this->assertSame(
            1,
            StudentAttendance::where('presensi_session_id', $session->id)
                ->where('student_id', $this->student1->id)
                ->count()
        );
    }
    public function test_qr_ditolak_setelah_presensi_manual_bulk_tanpa_session_id(): void
    {
        $session = $this->makeOpenSession();

        $generateResponse = $this->actingAs($this->teacherUser, 'sanctum')
            ->postJson('/api/v1/attendance/generate-qr', [
                'session_id' => $session->id,
            ])
            ->assertCreated();

        $this->actingAs($this->teacherUser, 'sanctum')
            ->postJson('/api/v1/attendances/bulk', [
                'class_id' => $this->class->id,
                'date' => $session->date,
                'attendances' => [
                    ['student_id' => $this->student1->id, 'status' => AttendanceStatus::Present->value],
                ],
            ])
            ->assertOk();

        $this->assertDatabaseHas('student_attendances', [
            'student_id' => $this->student1->id,
            'presensi_session_id' => $session->id,
            'status' => AttendanceStatus::Present->value,
        ]);

        $studentUser = User::create([
            'name' => $this->student1->name,
            'email' => 'siswa.bulk.manual@test.com',
            'password' => bcrypt('password'),
            'role' => 'student',
            'school_id' => $this->school->id,
        ]);

        $this->actingAs($studentUser, 'sanctum')
            ->postJson('/api/v1/attendance/scan', [
                'session_id' => $session->id,
                'token' => $generateResponse->json('data.token'),
            ])
            ->assertStatus(422)
            ->assertJsonPath('message', 'Anda sudah melakukan presensi.');

        $this->assertSame(
            1,
            StudentAttendance::where('student_id', $this->student1->id)
                ->where('date', $session->date)
                ->count()
        );
    }
    public function test_guru_dapat_generate_qr_token(): void
    {
        $session = $this->makeOpenSession();

        $response = $this->actingAs($this->teacherUser, 'sanctum')
            ->postJson('/api/v1/attendance/generate-qr', [
                'session_id' => $session->id,
            ]);

        $response->assertCreated()
            ->assertJsonStructure([
                'data' => ['token', 'qr_token', 'session_id', 'class_id', 'date'],
            ]);

        $token = $response->json('data.token');

        $this->assertNotEmpty($token);
        $this->assertDatabaseHas('presensi_sessions', [
            'id' => $session->id,
            'qr_token' => $token,
        ]);

        $secondResponse = $this->actingAs($this->teacherUser, 'sanctum')
            ->postJson('/api/v1/attendance/generate-qr', [
                'session_id' => $session->id,
            ]);

        $secondResponse->assertCreated()
            ->assertJsonPath('data.token', $token);
    }

    public function test_qr_statis_tetap_sukses_selama_sesi_open(): void
    {
        $session = $this->makeOpenSession();

        $generateResponse = $this->actingAs($this->teacherUser, 'sanctum')
            ->postJson('/api/v1/attendance/generate-qr', [
                'session_id' => $session->id,
            ])
            ->assertCreated();

        $token = $generateResponse->json('data.token');

        Carbon::setTestNow(Carbon::now()->addMinutes(5));

        $this->actingAs($this->teacherUser, 'sanctum')
            ->getJson("/api/v1/presensi-sessions/{$session->id}/qr")
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.token', $token)
            ->assertJsonStructure([
                'data' => ['session_id', 'qr_token', 'token', 'status', 'date', 'start_time', 'end_time'],
            ]);

        Carbon::setTestNow();
    }

    /* ═══════════════════════════════════════════════════════════
     *  TEST 6: POST /attendance/close
     * ═══════════════════════════════════════════════════════════ */
    public function test_guru_dapat_menutup_sesi(): void
    {
        $session = $this->makeOpenSession();

        $response = $this->actingAs($this->teacherUser, 'sanctum')
            ->postJson('/api/v1/attendance/close', [
                'session_id' => $session->id,
            ]);

        $response->assertOk()
            ->assertJsonPath('data.status', SessionStatus::Closed->value);

        $this->assertDatabaseHas('presensi_sessions', [
            'id'        => $session->id,
            'status'    => SessionStatus::Closed->value,
            'closed_by' => $this->teacherUser->id,
        ]);
    }

    /* ═══════════════════════════════════════════════════════════
     *  TEST 7: QR tidak bisa di-generate di sesi CLOSED
     * ═══════════════════════════════════════════════════════════ */
    public function test_qr_tidak_valid_setelah_sesi_ditutup(): void
    {
        $session = PresensiSession::create([
            'school_id'   => $this->school->id,
            'schedule_id' => $this->schedule->id,
            'teacher_id'  => $this->teacher->id,
            'opened_by'   => $this->teacherUser->id,
            'opened_at'   => now()->subMinutes(30),
            'date'        => Carbon::today()->toDateString(),
            'start_time'  => $this->classHour->start_time,
            'end_time'    => $this->classHour->end_time,
            'status'      => SessionStatus::Closed->value,
            'closed_by'   => $this->teacherUser->id,
            'closed_at'   => now(),
        ]);

        // Generate QR pada sesi closed → harus gagal (policy check)
        $response = $this->actingAs($this->teacherUser, 'sanctum')
            ->postJson('/api/v1/attendance/generate-qr', [
                'session_id' => $session->id,
            ]);

        // 403 karena policy: generateQr hanya boleh saat status OPEN
        $response->assertStatus(403);
    }

    /* ═══════════════════════════════════════════════════════════
     *  TEST 8: GET /attendance/session/{id}
     * ═══════════════════════════════════════════════════════════ */
    public function test_guru_dapat_melihat_detail_sesi(): void
    {
        $session = $this->makeOpenSession();

        $response = $this->actingAs($this->teacherUser, 'sanctum')
            ->getJson("/api/v1/attendance/session/{$session->id}");

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'id', 'date', 'status', 'status_label',
                    'start_time', 'end_time', 'opened_at',
                ],
            ]);
    }

    /* ═══════════════════════════════════════════════════════════
     *  TEST 9: GET /attendance/history
     * ═══════════════════════════════════════════════════════════ */
    public function test_guru_dapat_melihat_riwayat_presensi(): void
    {
        PresensiSession::create([
            'school_id'   => $this->school->id,
            'schedule_id' => $this->schedule->id,
            'teacher_id'  => $this->teacher->id,
            'date'        => Carbon::yesterday()->toDateString(),
            'status'      => SessionStatus::Closed->value,
        ]);

        $response = $this->actingAs($this->teacherUser, 'sanctum')
            ->getJson('/api/v1/attendance/history');

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'data' => ['total', 'sessions'],
            ]);

        $this->assertGreaterThanOrEqual(1, $response->json('data.total'));
    }

    /* ═══════════════════════════════════════════════════════════
     *  TEST 10: QR Token expired tidak bisa digunakan
     * ═══════════════════════════════════════════════════════════ */
    public function test_qr_token_expired_tidak_dapat_digunakan(): void
    {
        $session = $this->makeOpenSession();

        // Token yang sudah expired
        $expiredToken = QrToken::create([
            'presensi_session_id' => $session->id,
            'token'               => Str::random(40),
            'expired_at'          => Carbon::now()->subMinutes(1),
            'used'                => false,
        ]);

        // Student user
        $studentUser = User::create([
            'name'      => 'Siswa Test User',
            'email'     => 'siswa@test.com',
            'password'  => bcrypt('password'),
            'role'      => 'student',
            'school_id' => $this->school->id,
        ]);

        // Update siswa agar bisa ditemukan via nama user
        $this->student1->update(['name' => $studentUser->name]);

        $response = $this->actingAs($studentUser, 'sanctum')
            ->postJson('/api/v1/attendance/scan', [
                'token' => $expiredToken->token,
            ]);

        // 422 karena token sudah expired
        $response->assertStatus(422)
            ->assertJsonPath('success', false);
    }
}


