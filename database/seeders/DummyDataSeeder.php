<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. School ──────────────────────────────────────────────────────────
        $school = \App\Models\School::firstOrCreate(
            ['npsn' => '20000001'],
            [
                'name'           => 'SMK Negeri 1 Demo',
                'address'        => 'Jl. Pendidikan No.1, Kota Demo',
                'phone'          => '02112345678',
                'email'          => 'info@smkn1demo.sch.id',
                'principal_name' => 'Drs. Kepala Sekolah',
                'level'          => 'smk',
                'status'         => 'active',
            ]
        );
        $this->command->info("School: {$school->name}");

        // ── 2. Academic Year ───────────────────────────────────────────────────
        $academicYear = \App\Models\AcademicYear::firstOrCreate(
            ['school_id' => $school->id, 'name' => '2026/2027'],
            [
                'start_year' => 2026,
                'end_year'   => 2027,
                'start_date' => '2026-07-01',
                'end_date'   => '2027-06-30',
                'is_active'  => true,
            ]
        );
        $this->command->info("Academic Year: {$academicYear->name}");

        // ── 3. Semester ────────────────────────────────────────────────────────
        $semester = \App\Models\Semester::firstOrCreate(
            ['academic_year_id' => $academicYear->id, 'type' => 'odd'],
            [
                'name'       => 'Semester Ganjil 2026/2027',
                'start_date' => '2026-07-01',
                'end_date'   => '2026-12-31',
                'is_active'  => true,
            ]
        );
        $this->command->info("Semester: {$semester->name}");

        // ── 4. Teacher User ────────────────────────────────────────────────────
        $teacherUser = \App\Models\User::firstOrCreate(
            ['email' => 'guru@smkn1demo.sch.id'],
            [
                'name'      => 'Budi Santoso, S.Kom.',
                'password'  => Hash::make('password'),
                'role'      => 'teacher',
                'school_id' => $school->id,
                'status'    => 'active',
            ]
        );
        $teacherUser->syncRoles(['teacher']);

        // ── 5. Teacher ─────────────────────────────────────────────────────────
        $teacher = \App\Models\Teacher::firstOrCreate(
            ['nip' => '198706052010011001'],
            [
                'school_id'         => $school->id,
                'user_id'           => $teacherUser->id,
                'name'              => 'Budi Santoso, S.Kom.',
                'gender'            => 'male',
                'employment_status' => 'pns',
                'status'            => 'active',
            ]
        );
        $this->command->info("Teacher: {$teacher->name}");

        // ── 6. Subject ─────────────────────────────────────────────────────────
        $subject = \App\Models\Subject::firstOrCreate(
            ['school_id' => $school->id, 'code' => 'RPL-01'],
            [
                'name'       => 'Pemrograman Web',
                'short_name' => 'PWB',
                'group'      => 'specialized',
                'status'     => 'active',
            ]
        );
        $this->command->info("Subject: {$subject->name}");

        // ── 7. Class Hour ──────────────────────────────────────────────────────
        $classHours = [
            ['code' => 'J1', 'start_time' => '07:00:00', 'end_time' => '07:45:00', 'duration_minutes' => 45, 'order' => 1],
            ['code' => 'J2', 'start_time' => '07:45:00', 'end_time' => '08:30:00', 'duration_minutes' => 45, 'order' => 2],
            ['code' => 'J3', 'start_time' => '08:30:00', 'end_time' => '09:15:00', 'duration_minutes' => 45, 'order' => 3],
            ['code' => 'IST', 'start_time' => '09:15:00', 'end_time' => '09:30:00', 'duration_minutes' => 15, 'order' => 4, 'is_break' => true],
            ['code' => 'J4', 'start_time' => '09:30:00', 'end_time' => '10:15:00', 'duration_minutes' => 45, 'order' => 5],
        ];

        $classHour = null;
        foreach ($classHours as $ch) {
            $record = \App\Models\ClassHour::firstOrCreate(
                ['school_id' => $school->id, 'code' => $ch['code']],
                array_merge($ch, ['school_id' => $school->id])
            );
            if ($ch['code'] === 'J1') {
                $classHour = $record;
            }
        }
        $this->command->info("Class Hours: 5 jam pelajaran dibuat.");

        // ── 8. Class ───────────────────────────────────────────────────────────
        $class = \App\Models\StudentClass::firstOrCreate(
            [
                'school_id'        => $school->id,
                'name'             => 'XII RPL 1',
                'major'            => 'RPL',
                'academic_year_id' => $academicYear->id,
            ],
            [
                'grade'    => '12',
                'capacity' => 30,
                'status'   => 'active',
            ]
        );
        $this->command->info("Class: {$class->name}");

        // ── 9. Schedule ────────────────────────────────────────────────────────
        $dayToday = strtolower(now()->format('l'));
        // Buat jadwal untuk hari ini dan beberapa hari lain
        $days = [$dayToday, 'monday', 'tuesday', 'wednesday'];
        $days = array_unique($days);

        foreach ($days as $day) {
            try {
                \App\Models\Schedule::firstOrCreate(
                    [
                        'class_id'      => $class->id,
                        'class_hour_id' => $classHour->id,
                        'day'           => $day,
                        'semester_id'   => $semester->id,
                    ],
                    [
                        'school_id'  => $school->id,
                        'subject_id' => $subject->id,
                        'teacher_id' => $teacher->id,
                        'is_active'  => true,
                    ]
                );
            } catch (\Throwable $e) {
                // Jadwal mungkin sudah ada dengan kombinasi berbeda, lanjutkan
            }
        }
        $this->command->info("Schedules: jadwal untuk hari [{$dayToday}] dan hari lain dibuat.");

        // ── 10. Students ───────────────────────────────────────────────────────
        $students = [
            [
                'nis'          => 'NIS001',
                'nisn'         => '1000000001',
                'name'         => 'Ahmad Fauzi',
                'gender'       => 'male',
                'birth_date'   => '2008-03-10',
                'parent_phone' => '628111111111',
                'parent_name'  => 'Fauzi Sr.',
            ],
            [
                'nis'          => 'NIS002',
                'nisn'         => '1000000002',
                'name'         => 'Siti Rahayu',
                'gender'       => 'female',
                'birth_date'   => '2008-05-22',
                'parent_phone' => '628222222222',
                'parent_name'  => 'Rahayu Sr.',
            ],
            [
                'nis'          => 'NIS003',
                'nisn'         => '1000000003',
                'name'         => 'Budi Prakoso',
                'gender'       => 'male',
                'birth_date'   => '2008-08-14',
                'parent_phone' => '628333333333',
                'parent_name'  => 'Prakoso Sr.',
            ],
        ];

        foreach ($students as $s) {
            $student = \App\Models\Student::firstOrCreate(
                ['nis' => $s['nis']],
                array_merge($s, [
                    'school_id'       => $school->id,
                    'class_id'        => $class->id,
                    'status'          => 'active',
                    'enrollment_year' => 2024,
                ])
            );

            // Buat user siswa jika belum ada
            $studentUser = \App\Models\User::firstOrCreate(
                ['email' => strtolower(str_replace(' ', '.', $s['name'])) . '@smkn1demo.sch.id'],
                [
                    'name'      => $s['name'],
                    'password'  => Hash::make('password'),
                    'role'      => 'student',
                    'school_id' => $school->id,
                    'status'    => 'active',
                ]
            );
            $studentUser->syncRoles(['student']);

            $this->command->info("Student: {$student->name} (NIS: {$student->nis})");
        }

        // ── 11. Admin User ─────────────────────────────────────────────────────
        $adminUser = \App\Models\User::firstOrCreate(
            ['email' => 'admin@smkn1demo.sch.id'],
            [
                'name'      => 'Admin Sekolah',
                'password'  => Hash::make('password'),
                'role'      => 'admin',
                'school_id' => $school->id,
                'status'    => 'active',
            ]
        );
        $adminUser->syncRoles(['admin']);
        $this->command->info("Admin: {$adminUser->email}");

        // ── 12. Super Admin ────────────────────────────────────────────────────
        $superAdmin = \App\Models\User::firstOrCreate(
            ['email' => 'superadmin@simpad.app'],
            [
                'name'      => 'Super Admin',
                'password'  => Hash::make('password'),
                'role'      => 'super_admin',
                'school_id' => null,
                'status'    => 'active',
            ]
        );
        $superAdmin->syncRoles(['super_admin']);
        $this->command->info("Super Admin: {$superAdmin->email}");

        // ── 13. Alumni & Alumni Profiles ───────────────────────────────────────
        $alumniData = [
            [
                'nisn' => '0051234501',
                'name' => 'Faisal Rahman',
                'gender' => 'male',
                'graduation_year' => 2024,
                'class_name' => 'XII RPL 1',
                'major' => 'RPL',
                'email' => 'faisal.rahman@test.local',
                'status' => 'working',
                'company' => 'PT Solusi Teknologi Indonesia',
                'position' => 'Junior Web Developer',
                'industry' => 'IT & Software',
            ],
            [
                'nisn' => '0051234502',
                'name' => 'Dewi Lestari',
                'gender' => 'female',
                'graduation_year' => 2024,
                'class_name' => 'XII RPL 2',
                'major' => 'RPL',
                'email' => 'dewi.lestari@test.local',
                'status' => 'studying',
                'university' => 'Universitas Indonesia',
                'study_program' => 'Ilmu Komputer',
            ],
            [
                'nisn' => '0051234503',
                'name' => 'Hendra Wijaya',
                'gender' => 'male',
                'graduation_year' => 2023,
                'class_name' => 'XII RPL 1',
                'major' => 'RPL',
                'email' => 'hendra.wijaya@test.local',
                'status' => 'entrepreneur',
                'business_name' => 'Wira Digital Studio',
                'business_field' => 'Jasa Desain & IT',
            ],
            [
                'nisn' => '0051234504',
                'name' => 'Rina Mutia',
                'gender' => 'female',
                'graduation_year' => 2023,
                'class_name' => 'XII RPL 2',
                'major' => 'RPL',
                'email' => 'rina.mutia@test.local',
                'status' => 'studying_working',
                'university' => 'Universitas Bina Nusantara',
                'study_program' => 'Sistem Informasi',
                'company' => 'Freelance Coder',
                'position' => 'Technical Writer',
            ],
            [
                'nisn' => '0051234505',
                'name' => 'Aditya Pratama',
                'gender' => 'male',
                'graduation_year' => 2024,
                'class_name' => 'XII RPL 1',
                'major' => 'RPL',
                'email' => 'aditya.pratama@test.local',
                'status' => 'unemployed',
            ],
            [
                'nisn' => '0051234506',
                'name' => 'Sania Rahma',
                'gender' => 'female',
                'graduation_year' => 2024,
                'class_name' => 'XII RPL 1',
                'major' => 'RPL',
                'email' => 'sania.rahma@test.local',
                'status' => 'working',
                'company' => 'Tokopedia',
                'position' => 'Associate Product Manager',
                'industry' => 'E-Commerce',
            ],
            [
                'nisn' => '0051234507',
                'name' => 'Rian Hidayat',
                'gender' => 'male',
                'graduation_year' => 2023,
                'class_name' => 'XII RPL 2',
                'major' => 'RPL',
                'email' => 'rian.hidayat@test.local',
                'status' => 'working',
                'company' => 'Shopee Indonesia',
                'position' => 'Data Analyst Intern',
                'industry' => 'E-Commerce',
            ],
        ];

        foreach ($alumniData as $data) {
            $alumni = \App\Models\Alumni::firstOrCreate(
                ['nisn' => $data['nisn']],
                [
                    'school_id' => $school->id,
                    'name' => $data['name'],
                    'gender' => $data['gender'],
                    'graduation_year' => $data['graduation_year'],
                    'class_name' => $data['class_name'],
                    'major' => $data['major'],
                    'email' => $data['email'],
                    'phone' => '62812' . rand(1111111, 9999999),
                    'verification_status' => 'verified',
                    'verified_by' => $adminUser->id,
                    'verified_at' => now(),
                ]
            );

            // Buat user account untuk alumni
            $alumniUser = \App\Models\User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password'),
                    'role' => 'alumni',
                    'school_id' => $school->id,
                    'status' => 'active',
                ]
            );
            $alumniUser->syncRoles(['alumni']);
            $alumni->update(['user_id' => $alumniUser->id]);

            // Buat profile alumni
            \App\Models\AlumniProfile::firstOrCreate(
                ['alumni_id' => $alumni->id],
                [
                    'current_status' => $data['status'],
                    'university_name' => $data['university'] ?? null,
                    'study_program' => $data['study_program'] ?? null,
                    'company_name' => $data['company'] ?? null,
                    'job_position' => $data['position'] ?? null,
                    'industry' => $data['industry'] ?? null,
                    'business_name' => $data['business_name'] ?? null,
                    'business_field' => $data['business_field'] ?? null,
                    'city' => 'Jakarta',
                    'province' => 'DKI Jakarta',
                    'last_updated_at' => now(),
                ]
            );
        }
        $this->command->info("Alumni: " . count($alumniData) . " data alumni & profil berhasil di-seed.");

        $this->command->newLine();
        $this->command->info('=== DUMMY DATA BERHASIL DIBUAT ===');
        $this->command->info('Akun Login:');
        $this->command->info('  Super Admin : superadmin@simpad.app       / password');
        $this->command->info('  Admin       : admin@smkn1demo.sch.id      / password');
        $this->command->info('  Guru        : guru@smkn1demo.sch.id       / password');
        $this->command->info('  Siswa 1     : ahmad.fauzi@smkn1demo.sch.id/ password');
        $this->command->info('  Siswa 2     : siti.rahayu@smkn1demo.sch.id/ password');
    }
}
