<?php

namespace Tests\Feature;

use App\Enums\AttendanceStatus;
use App\Jobs\SendWhatsAppNotification;
use App\Models\AcademicYear;
use App\Models\ClassHour;
use App\Models\Schedule;
use App\Models\School;
use App\Models\Semester;
use App\Models\Student;
use App\Models\StudentClass;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class MobileTeacherAttendanceFlowTest extends TestCase
{
    use RefreshDatabase;

    private User $teacherUser;
    private Teacher $teacher;
    private School $school;
    private StudentClass $class;
    private User $parentUser;
    private array $students = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedDatabase();
    }

    private function seedDatabase(): void
    {
        // 1. Seed Roles & Permissions
        $this->artisan('db:seed', ['--class' => 'RoleAndPermissionSeeder']);

        // 2. Create School
        $this->school = School::create([
            'name'  => 'SMK Digital Presensi',
            'npsn'  => '10203040',
            'level' => 'smk',
        ]);

        // 3. Create Teacher User & Teacher Profile
        $this->teacherUser = User::create([
            'name'      => 'Guru Mobile',
            'email'     => 'gurumobile@sekolah.id',
            'password'  => bcrypt('password123'),
            'role'      => 'teacher',
            'school_id' => $this->school->id,
        ]);

        $this->teacher = Teacher::create([
            'school_id' => $this->school->id,
            'user_id'   => $this->teacherUser->id,
            'name'      => 'Guru Mobile, S.Pd.',
            'nip'       => '198501012010011001',
            'status'    => 'active',
        ]);

        // 4. Create Class (where teacher is homeroom teacher or assigned via schedule)
        $this->class = StudentClass::create([
            'school_id'           => $this->school->id,
            'homeroom_teacher_id' => $this->teacher->id,
            'name'                => 'X RPL 1',
            'grade'               => '10',
            'major'               => 'Rekayasa Perangkat Lunak',
        ]);

        // 5. Create Parent User with Phone Number (for WhatsApp notification)
        $this->parentUser = User::create([
            'name'      => 'Bapak/Ibu Wali Siswa',
            'email'     => 'wali@ortu.id',
            'phone'     => '081234567890',
            'password'  => bcrypt('password123'),
            'role'      => 'parent',
            'school_id' => $this->school->id,
        ]);

        // 6. Create 5 Students (representing all 5 attendance statuses: Hadir, Terlambat, Izin, Sakit, Alpha)
        $names = ['Ahmad Hadir', 'Budi Terlambat', 'Citra Izin', 'Dewi Sakit', 'Eko Alpha'];
        foreach ($names as $index => $name) {
            $this->students[] = Student::create([
                'school_id'      => $this->school->id,
                'class_id'       => $this->class->id,
                'parent_user_id' => $this->parentUser->id,
                'nis'            => '20260' . ($index + 1),
                'nisn'           => '000202600' . ($index + 1),
                'name'           => $name,
                'gender'         => $index % 2 === 0 ? 'male' : 'female',
                'birth_date'     => '2010-01-01',
                'status'         => 'active',
            ]);
        }
    }

    /**
     * Test lengkap alur presensi mobile guru dari awal hingga akhir:
     * Guru Login → Pilih Kelas → Pilih Tanggal → Input Status (5 Status) → Simpan → WA Orang Tua
     */
    public function test_alur_presensi_mobile_guru_lengkap(): void
    {
        Queue::fake([SendWhatsAppNotification::class]);

        // ─── Step 1: Guru Login ──────────────────────────────────────────────
        $loginResponse = $this->postJson('/api/v1/login', [
            'email'    => 'gurumobile@sekolah.id',
            'password' => 'password123',
        ]);

        $loginResponse->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'data' => [
                    'token',
                    'token_type',
                    'user' => ['id', 'name', 'email', 'role'],
                ],
            ]);

        $token = $loginResponse->json('data.token');
        $this->assertNotEmpty($token);

        // ─── Step 2: Pilih Kelas ─────────────────────────────────────────────
        // Guru melihat daftar kelas yang diampunya
        $classesResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/classes');

        $classesResponse->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonFragment([
                'id'   => $this->class->id,
                'name' => 'X RPL 1',
            ]);

        // ─── Step 3: Pilih Tanggal & Lihat Daftar Siswa ──────────────────────
        $testDate = Carbon::today()->toDateString();
        $studentsResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson("/api/v1/classes/{$this->class->id}/students?date={$testDate}");

        $studentsResponse->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.class.id', $this->class->id)
            ->assertJsonPath('data.date', $testDate)
            ->assertJsonCount(5, 'data.students');

        // ─── Step 4: Input Status (Hadir, Terlambat, Izin, Sakit, Alpha) & Simpan ─
        $attendancesInput = [
            [
                'student_id' => $this->students[0]->id,
                'status'     => AttendanceStatus::Present->value, // Hadir
                'note'       => 'Hadir tepat waktu',
            ],
            [
                'student_id' => $this->students[1]->id,
                'status'     => AttendanceStatus::Late->value,    // Terlambat
                'note'       => 'Terlambat 15 menit karena ban bocor',
            ],
            [
                'student_id' => $this->students[2]->id,
                'status'     => AttendanceStatus::Permission->value, // Izin
                'note'       => 'Izin ada acara keluarga',
            ],
            [
                'student_id' => $this->students[3]->id,
                'status'     => AttendanceStatus::Sick->value,    // Sakit
                'note'       => 'Sakit demam tinggi',
            ],
            [
                'student_id' => $this->students[4]->id,
                'status'     => AttendanceStatus::Absent->value,  // Alpha
                'note'       => 'Tanpa keterangan',
            ],
        ];

        $saveResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/attendance/submit', [
                'class_id'    => $this->class->id,
                'date'        => $testDate,
                'attendances' => $attendancesInput,
            ]);

        $saveResponse->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.count', 5);

        // Verifikasi database bahwa ke-5 status tersimpan dengan benar
        $this->assertDatabaseHas('student_attendances', [
            'student_id' => $this->students[0]->id,
            'status'     => 'present',
            'note'       => 'Hadir tepat waktu',
        ]);
        $this->assertDatabaseHas('student_attendances', [
            'student_id' => $this->students[1]->id,
            'status'     => 'late',
            'note'       => 'Terlambat 15 menit karena ban bocor',
        ]);
        $this->assertDatabaseHas('student_attendances', [
            'student_id' => $this->students[2]->id,
            'status'     => 'permission',
            'note'       => 'Izin ada acara keluarga',
        ]);
        $this->assertDatabaseHas('student_attendances', [
            'student_id' => $this->students[3]->id,
            'status'     => 'sick',
            'note'       => 'Sakit demam tinggi',
        ]);
        $this->assertDatabaseHas('student_attendances', [
            'student_id' => $this->students[4]->id,
            'status'     => 'absent',
            'note'       => 'Tanpa keterangan',
        ]);

        // ─── Step 5: Verifikasi Notifikasi WA Orang Tua Dispatched ───────────
        // Karena 5 siswa memiliki parent dengan nomor telepon (081234567890), harus ada 5 job terkirim
        Queue::assertPushed(SendWhatsAppNotification::class, 5);
    }
}
