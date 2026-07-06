<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🚀 Memulai seeding Data Dummy Super Lengkap & Real-Time...');

        // ── 1. School ──────────────────────────────────────────────────────────
        $school = \App\Models\School::firstOrCreate(
            ['npsn' => '20260001'],
            [
                'name'           => 'SMK Negeri 1 Digital Presensi & Alumni',
                'address'        => 'Jl. Teknologi Nusantara No. 10, Jakarta Selatan',
                'phone'          => '021-78901234',
                'email'          => 'info@smkn1digital.sch.id',
                'principal_name' => 'Dr. Ir. H. Hendra Nusantara, M.M.',
                'level'          => 'smk',
                'status'         => 'active',
            ]
        );
        $this->command->info("🏫 School: {$school->name}");

        // ── 2. Academic Years & Semesters ──────────────────────────────────────
        $academicYear2025 = \App\Models\AcademicYear::firstOrCreate(
            ['school_id' => $school->id, 'name' => '2025/2026'],
            [
                'start_year' => 2025,
                'end_year'   => 2026,
                'start_date' => '2025-07-01',
                'end_date'   => '2026-06-30',
                'is_active'  => false,
            ]
        );

        $academicYear2026 = \App\Models\AcademicYear::firstOrCreate(
            ['school_id' => $school->id, 'name' => '2026/2027'],
            [
                'start_year' => 2026,
                'end_year'   => 2027,
                'start_date' => '2026-07-01',
                'end_date'   => '2027-06-30',
                'is_active'  => true,
            ]
        );

        $semesterOdd = \App\Models\Semester::firstOrCreate(
            ['academic_year_id' => $academicYear2026->id, 'type' => 'odd'],
            [
                'name'       => 'Semester Ganjil 2026/2027',
                'start_date' => '2026-07-01',
                'end_date'   => '2026-12-31',
                'is_active'  => true,
            ]
        );

        $semesterEven = \App\Models\Semester::firstOrCreate(
            ['academic_year_id' => $academicYear2026->id, 'type' => 'even'],
            [
                'name'       => 'Semester Genap 2026/2027',
                'start_date' => '2027-01-01',
                'end_date'   => '2027-06-30',
                'is_active'  => false,
            ]
        );
        $this->command->info("📅 Academic Year & Semesters dibuat.");

        // ── 3. Subjects (Mata Pelajaran) ───────────────────────────────────────
        $subjectsData = [
            ['code' => 'PWB', 'name' => 'Pemrograman Web & Perangkat Bergerak', 'short_name' => 'Web Dev', 'group' => 'specialized'],
            ['code' => 'MTK', 'name' => 'Matematika', 'short_name' => 'MTK', 'group' => 'general'],
            ['code' => 'BIN', 'name' => 'Bahasa Indonesia', 'short_name' => 'B. Indo', 'group' => 'general'],
            ['code' => 'BIG', 'name' => 'Bahasa Inggris', 'short_name' => 'B. Inggris', 'group' => 'general'],
            ['code' => 'BSD', 'name' => 'Basis Data & Pemodelan', 'short_name' => 'Basis Data', 'group' => 'specialized'],
            ['code' => 'JRK', 'name' => 'Administrasi Infrastruktur Jaringan', 'short_name' => 'Jaringan', 'group' => 'specialized'],
            ['code' => 'PKK', 'name' => 'Produk Kreatif & Kewirausahaan', 'short_name' => 'PKK', 'group' => 'specialized'],
            ['code' => 'DGR', 'name' => 'Desain Grafis & Multimedia', 'short_name' => 'Desain', 'group' => 'specialized'],
            ['code' => 'SEJ', 'name' => 'Sejarah Indonesia', 'short_name' => 'Sejarah', 'group' => 'general'],
            ['code' => 'PJK', 'name' => 'Pendidikan Jasmani & Olahraga', 'short_name' => 'PJOK', 'group' => 'general'],
        ];

        $subjects = [];
        foreach ($subjectsData as $s) {
            $subjects[$s['code']] = \App\Models\Subject::firstOrCreate(
                ['school_id' => $school->id, 'code' => $s['code']],
                array_merge($s, ['school_id' => $school->id, 'status' => 'active'])
            );
        }
        $this->command->info("📚 10 Mata Pelajaran dibuat.");

        // ── 4. Teachers (10 Guru Lengkap) ──────────────────────────────────────
        $teachersData = [
            ['email' => 'guru@smkn1demo.sch.id', 'name' => 'Budi Santoso, S.Kom.', 'nip' => '198706052010011001', 'gender' => 'male', 'subject' => 'PWB'],
            ['email' => 'dewi.lestari@sekolah.id', 'name' => 'Dewi Lestari, S.Pd.', 'nip' => '198807122011012002', 'gender' => 'female', 'subject' => 'MTK'],
            ['email' => 'ahmad.fauzi@sekolah.id', 'name' => 'Ahmad Fauzi, M.Pd.', 'nip' => '198503152009011003', 'gender' => 'male', 'subject' => 'BIN'],
            ['email' => 'siti.rahayu@sekolah.id', 'name' => 'Siti Rahayu, S.S.', 'nip' => '199001202014012004', 'gender' => 'female', 'subject' => 'BIG'],
            ['email' => 'hendra.wijaya@sekolah.id', 'name' => 'Hendra Wijaya, S.Kom.', 'nip' => '198905102013011005', 'gender' => 'male', 'subject' => 'BSD'],
            ['email' => 'rina.mutia@sekolah.id', 'name' => 'Rina Mutia, S.T.', 'nip' => '199108252015012006', 'gender' => 'female', 'subject' => 'JRK'],
            ['email' => 'aditya.pratama@sekolah.id', 'name' => 'Aditya Pratama, S.E.', 'nip' => '198604182010011007', 'gender' => 'male', 'subject' => 'PKK'],
            ['email' => 'faisal.rahman@sekolah.id', 'name' => 'Faisal Rahman, M.Kom.', 'nip' => '199211052016011008', 'gender' => 'male', 'subject' => 'DGR'],
            ['email' => 'sania.rahma@sekolah.id', 'name' => 'Sania Rahma, S.Pd.', 'nip' => '199302142017012009', 'gender' => 'female', 'subject' => 'SEJ'],
            ['email' => 'rian.hidayat@sekolah.id', 'name' => 'Rian Hidayat, S.Or.', 'nip' => '198809302012011010', 'gender' => 'male', 'subject' => 'PJK'],
        ];

        $teachers = [];
        foreach ($teachersData as $idx => $t) {
            $user = \App\Models\User::firstOrCreate(
                ['email' => $t['email']],
                [
                    'name'      => $t['name'],
                    'password'  => Hash::make('password'),
                    'role'      => 'teacher',
                    'school_id' => $school->id,
                    'status'    => 'active',
                ]
            );
            $user->syncRoles(['teacher']);

            $teacher = \App\Models\Teacher::firstOrCreate(
                ['nip' => $t['nip']],
                [
                    'school_id'         => $school->id,
                    'user_id'           => $user->id,
                    'name'              => $t['name'],
                    'gender'            => $t['gender'],
                    'employment_status' => 'pns',
                    'status'            => 'active',
                ]
            );
            $teachers[] = $teacher;
        }
        $this->command->info("👨‍🏫 10 Guru Pengajar dibuat & disinkronkan.");

        // ── 5. Class Hours (8 Jam Pelajaran + Istirahat) ───────────────────────
        $classHoursData = [
            ['code' => 'J1', 'start_time' => '07:00:00', 'end_time' => '07:45:00', 'duration_minutes' => 45, 'order' => 1],
            ['code' => 'J2', 'start_time' => '07:45:00', 'end_time' => '08:30:00', 'duration_minutes' => 45, 'order' => 2],
            ['code' => 'J3', 'start_time' => '08:30:00', 'end_time' => '09:15:00', 'duration_minutes' => 45, 'order' => 3],
            ['code' => 'IST1', 'start_time' => '09:15:00', 'end_time' => '09:30:00', 'duration_minutes' => 15, 'order' => 4, 'is_break' => true],
            ['code' => 'J4', 'start_time' => '09:30:00', 'end_time' => '10:15:00', 'duration_minutes' => 45, 'order' => 5],
            ['code' => 'J5', 'start_time' => '10:15:00', 'end_time' => '11:00:00', 'duration_minutes' => 45, 'order' => 6],
            ['code' => 'J6', 'start_time' => '11:00:00', 'end_time' => '11:45:00', 'duration_minutes' => 45, 'order' => 7],
            ['code' => 'IST2', 'start_time' => '11:45:00', 'end_time' => '12:30:00', 'duration_minutes' => 45, 'order' => 8, 'is_break' => true],
            ['code' => 'J7', 'start_time' => '12:30:00', 'end_time' => '13:15:00', 'duration_minutes' => 45, 'order' => 9],
            ['code' => 'J8', 'start_time' => '13:15:00', 'end_time' => '14:00:00', 'duration_minutes' => 45, 'order' => 10],
        ];

        $classHours = [];
        foreach ($classHoursData as $ch) {
            $classHours[$ch['code']] = \App\Models\ClassHour::firstOrCreate(
                ['school_id' => $school->id, 'code' => $ch['code']],
                array_merge($ch, ['school_id' => $school->id])
            );
        }
        $this->command->info("⏰ 8 Jam Pelajaran & 2 Waktu Istirahat dibuat.");

        // ── 6. Classes (8 Kelas RPL & TKJ) ─────────────────────────────────────
        $classesData = [
            ['name' => 'X RPL 1', 'grade' => '10', 'major' => 'RPL', 'homeroom_idx' => 0],
            ['name' => 'X RPL 2', 'grade' => '10', 'major' => 'RPL', 'homeroom_idx' => 1],
            ['name' => 'X TKJ 1', 'grade' => '10', 'major' => 'TKJ', 'homeroom_idx' => 5],
            ['name' => 'XI RPL 1', 'grade' => '11', 'major' => 'RPL', 'homeroom_idx' => 2],
            ['name' => 'XI TKJ 1', 'grade' => '11', 'major' => 'TKJ', 'homeroom_idx' => 6],
            ['name' => 'XII RPL 1', 'grade' => '12', 'major' => 'RPL', 'homeroom_idx' => 3],
            ['name' => 'XII RPL 2', 'grade' => '12', 'major' => 'RPL', 'homeroom_idx' => 4],
            ['name' => 'XII TKJ 1', 'grade' => '12', 'major' => 'TKJ', 'homeroom_idx' => 7],
        ];

        $classes = [];
        foreach ($classesData as $c) {
            $class = \App\Models\StudentClass::firstOrCreate(
                [
                    'school_id'        => $school->id,
                    'name'             => $c['name'],
                    'academic_year_id' => $academicYear2026->id,
                ],
                [
                    'grade'               => $c['grade'],
                    'major'               => $c['major'],
                    'homeroom_teacher_id' => $teachers[$c['homeroom_idx']]->id,
                    'capacity'            => 36,
                    'status'              => 'active',
                ]
            );
            $classes[] = $class;
        }
        $this->command->info("🏛️ 8 Kelas beserta Wali Kelas dibuat.");

        // ── 7. Schedules (Jadwal Mengajar Padat Senin - Jumat + Hari Ini) ─────
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
        $todayDay = strtolower(now()->format('l'));
        if (!in_array($todayDay, ['saturday', 'sunday'])) {
            if (!in_array($todayDay, $days)) {
                $days[] = $todayDay;
            }
        } else {
            // Jika weekend, tetap masukkan todayDay agar saat dites hari ini jadwal tetap muncul
            $days[] = $todayDay;
        }
        $days = array_unique($days);

        $scheduleBlocks = [
            ['ch' => ['J1', 'J2'], 'subj' => 'PWB', 'teacher_idx' => 0],
            ['ch' => ['J3', 'J4'], 'subj' => 'MTK', 'teacher_idx' => 1],
            ['ch' => ['J5', 'J6'], 'subj' => 'BIN', 'teacher_idx' => 2],
            ['ch' => ['J7', 'J8'], 'subj' => 'BIG', 'teacher_idx' => 3],
            ['ch' => ['J1', 'J2'], 'subj' => 'BSD', 'teacher_idx' => 4],
            ['ch' => ['J3', 'J4'], 'subj' => 'JRK', 'teacher_idx' => 5],
            ['ch' => ['J5', 'J6'], 'subj' => 'PKK', 'teacher_idx' => 6],
            ['ch' => ['J7', 'J8'], 'subj' => 'DGR', 'teacher_idx' => 7],
        ];

        $scheduleCount = 0;
        foreach ($classes as $clsIdx => $class) {
            foreach ($days as $dayIdx => $day) {
                // Rotasi jadwal per hari agar variatif
                $blockOffset = ($clsIdx + $dayIdx) % count($scheduleBlocks);
                for ($b = 0; $b < 3; $b++) { // 3 blok mata pelajaran per hari
                    $block = $scheduleBlocks[($blockOffset + $b) % count($scheduleBlocks)];
                    foreach ($block['ch'] as $chCode) {
                        if (isset($classHours[$chCode]) && isset($subjects[$block['subj']])) {
                            try {
                                \App\Models\Schedule::firstOrCreate(
                                    [
                                        'class_id'      => $class->id,
                                        'class_hour_id' => $classHours[$chCode]->id,
                                        'day'           => $day,
                                        'semester_id'   => $semesterOdd->id,
                                    ],
                                    [
                                        'school_id'        => $school->id,
                                        'subject_id'       => $subjects[$block['subj']]->id,
                                        'teacher_id'       => $teachers[$block['teacher_idx']]->id,
                                        'is_active'        => true,
                                        'allow_early_open' => true,
                                    ]
                                );
                                $scheduleCount++;
                            } catch (\Throwable $e) {
                                // Abaikan duplikat
                            }
                        }
                    }
                }
            }
        }
        $this->command->info("🗓️ {$scheduleCount} Blok Jadwal Pelajaran (Senin-Jumat + Hari Ini) dibuat.");

        // ── 8. Students (160 Siswa, 20 per Kelas + Akun Orang Tua) ─────────────
        $firstNames = ['Ahmad', 'Budi', 'Citra', 'Dewi', 'Eko', 'Fitri', 'Galih', 'Hana', 'Indra', 'Joko', 'Kartika', 'Laras', 'Muhammad', 'Nur', 'Oscar', 'Putri', 'Qori', 'Rian', 'Siti', 'Tri'];
        $lastNames  = ['Fauzi', 'Santoso', 'Rahayu', 'Lestari', 'Prakoso', 'Pratama', 'Wijaya', 'Mutia', 'Rahman', 'Hidayat', 'Saputra', 'Wulandari', 'Kurniawan', 'Halim', 'Syahputra', 'Maharani', 'Huda', 'Nugroho', 'Anggraini', 'Utami'];

        $studentCount = 0;
        foreach ($classes as $clsIdx => $class) {
            for ($i = 0; $i < 20; $i++) {
                $fName = $firstNames[$i % count($firstNames)];
                $lName = $lastNames[($clsIdx + $i) % count($lastNames)];
                $fullName = "{$fName} {$lName} " . ($clsIdx + 1);
                $nis  = '2026' . sprintf('%02d', $clsIdx + 1) . sprintf('%03d', $i + 1);
                $nisn = '0008' . sprintf('%02d', $clsIdx + 1) . sprintf('%04d', $i + 1);
                $phone = '62812' . sprintf('%03d', $clsIdx + 1) . sprintf('%04d', $i + 1);

                // Create Parent Account
                $parentUser = \App\Models\User::firstOrCreate(
                    ['phone' => $phone],
                    [
                        'name'      => "Wali dari {$fullName}",
                        'email'     => "ortu_{$nis}@sekolah.id",
                        'password'  => Hash::make('password'),
                        'role'      => 'parent',
                        'school_id' => $school->id,
                        'status'    => 'active',
                    ]
                );
                $parentUser->syncRoles(['parent']);

                // Create Student Account
                $studentEmail = strtolower("{$fName}.{$lName}{$nis}@smkn1demo.sch.id");
                // Akun demo khusus untuk tes mudah
                if ($clsIdx === 0 && $i === 0) {
                    $studentEmail = 'siswa@smkn1demo.sch.id';
                }

                $studentUser = \App\Models\User::firstOrCreate(
                    ['email' => $studentEmail],
                    [
                        'name'      => $fullName,
                        'password'  => Hash::make('password'),
                        'role'      => 'student',
                        'school_id' => $school->id,
                        'status'    => 'active',
                    ]
                );
                $studentUser->syncRoles(['student']);

                \App\Models\Student::firstOrCreate(
                    ['nis' => $nis],
                    [
                        'school_id'       => $school->id,
                        'class_id'        => $class->id,
                        'parent_user_id'  => $parentUser->id,
                        'nisn'            => $nisn,
                        'name'            => $fullName,
                        'gender'          => ($i % 2 === 0) ? 'male' : 'female',
                        'birth_date'      => '2009-' . sprintf('%02d', ($i % 12) + 1) . '-15',
                        'status'          => 'active',
                    ]
                );
                $studentCount++;
            }
        }
        $this->command->info("👨‍🎓 {$studentCount} Siswa & Akun Orang Tua (WhatsApp siap) dibuat.");

        // ── 9. Admin & Super Admin ─────────────────────────────────────────────
        $adminUser = \App\Models\User::firstOrCreate(
            ['email' => 'admin@smkn1demo.sch.id'],
            [
                'name'      => 'Admin Sekolah Utama',
                'password'  => Hash::make('password'),
                'role'      => 'admin',
                'school_id' => $school->id,
                'status'    => 'active',
            ]
        );
        $adminUser->syncRoles(['admin']);

        $superAdmin = \App\Models\User::firstOrCreate(
            ['email' => 'superadmin@simpad.app'],
            [
                'name'      => 'Super Admin Sistem',
                'password'  => Hash::make('password'),
                'role'      => 'super_admin',
                'school_id' => null,
                'status'    => 'active',
            ]
        );
        $superAdmin->syncRoles(['super_admin']);
        $this->command->info("🛡️ Admin & Super Admin disinkronkan.");

        // ── 10. Alumni & Alumni Profiles (60+ Alumni Realistis 2021-2025) ──────
        $companies  = ['GoTo Group', 'Tokopedia', 'Shopee Indonesia', 'Traveloka', 'Bank BCA', 'Bank Mandiri', 'Telkomsel', 'PT Pertamina', 'Unilever Indonesia', 'Accenture', 'Microsoft Indonesia', 'Amazon Web Services', 'Bukalapak', 'Blibli', 'Tiket.com', 'PT Solusi Teknologi Nusantara', 'Halodoc', 'Ruangguru', 'Dana Indonesia', 'OVO'];
        $positions  = ['Software Engineer', 'Frontend Developer', 'Backend Developer', 'Fullstack Developer', 'UI/UX Designer', 'Data Analyst', 'Product Manager', 'Network Engineer', 'DevOps Engineer', 'Quality Assurance', 'IT Support Specialist', 'Digital Marketing Specialist', 'Cloud Engineer'];
        $universities = ['Universitas Indonesia (UI)', 'Institut Teknologi Bandung (ITB)', 'Universitas Gadjah Mada (UGM)', 'Institut Teknologi Sepuluh Nopember (ITS)', 'Universitas Bina Nusantara (Binus)', 'Telkom University', 'Universitas Padjadjaran (UNPAD)', 'Universitas Diponegoro (UNDIP)', 'Universitas Brawijaya (UB)'];
        $studyProgs = ['Teknik Informatika', 'Sistem Informasi', 'Ilmu Komputer', 'Teknik Komputer', 'Desain Komunikasi Visual', 'Manajemen Bisnis Telekomunikasi & Informatika'];

        $alumniCount = 0;
        for ($yr = 2021; $yr <= 2025; $yr++) {
            for ($j = 1; $j <= 12; $j++) {
                $fName = $firstNames[($yr + $j) % count($firstNames)];
                $lName = $lastNames[($yr * $j) % count($lastNames)];
                $name  = "{$fName} {$lName} (Alumni {$yr})";
                $nisn  = "00{$yr}" . sprintf('%04d', $j);
                $email = strtolower("{$fName}.{$lName}{$yr}@alumni.test");

                // Tentukan status karir secara realistis: 60% working, 25% studying, 10% entrepreneur, 5% unemployed
                $randStat = rand(1, 100);
                if ($randStat <= 60) {
                    $status = 'working';
                } elseif ($randStat <= 85) {
                    $status = 'studying';
                } elseif ($randStat <= 95) {
                    $status = 'entrepreneur';
                } else {
                    $status = 'unemployed';
                }

                $alumni = \App\Models\Alumni::firstOrCreate(
                    ['nisn' => $nisn],
                    [
                        'school_id'           => $school->id,
                        'name'                => $name,
                        'gender'              => ($j % 2 === 0) ? 'male' : 'female',
                        'graduation_year'     => $yr,
                        'class_name'          => ($j <= 6) ? "XII RPL 1" : "XII TKJ 1",
                        'major'               => ($j <= 6) ? 'RPL' : 'TKJ',
                        'email'               => $email,
                        'phone'               => '62813' . rand(10000000, 99999999),
                        'verification_status' => 'verified',
                        'verified_by'         => $adminUser->id,
                        'verified_at'         => now(),
                    ]
                );

                $alumniUser = \App\Models\User::firstOrCreate(
                    ['email' => $email],
                    [
                        'name'      => $name,
                        'password'  => Hash::make('password'),
                        'role'      => 'alumni',
                        'school_id' => $school->id,
                        'status'    => 'active',
                    ]
                );
                $alumniUser->syncRoles(['alumni']);
                $alumni->update(['user_id' => $alumniUser->id]);

                \App\Models\AlumniProfile::firstOrCreate(
                    ['alumni_id' => $alumni->id],
                    [
                        'current_status'  => $status,
                        'company_name'    => ($status === 'working') ? $companies[array_rand($companies)] : null,
                        'job_position'    => ($status === 'working') ? $positions[array_rand($positions)] : null,
                        'university_name' => ($status === 'studying') ? $universities[array_rand($universities)] : null,
                        'study_program'   => ($status === 'studying') ? $studyProgs[array_rand($studyProgs)] : null,
                        'business_name'   => ($status === 'entrepreneur') ? "{$fName} Digital Studio & Co." : null,
                        'city'            => 'Jakarta',
                        'province'        => 'DKI Jakarta',
                    ]
                );
                $alumniCount++;
            }
        }
        $this->command->info("🎓 {$alumniCount} Alumni & Profil Karir (2021-2025) berhasil dibuat.");

        // ── 11. Job Vacancies (10 Lowongan Kerja Realistis) ───────────────────
        $jobsData = [
            ['title' => 'Junior Fullstack Laravel & Vue Developer', 'company' => 'PT Solusi Teknologi Nusantara', 'salary_min' => 6000000, 'salary_max' => 9000000, 'type' => 'full_time', 'cat' => 'technology'],
            ['title' => 'Network & Infrastructure Support Specialist', 'company' => 'Telkomsel Indonesia', 'salary_min' => 7000000, 'salary_max' => 10000000, 'type' => 'full_time', 'cat' => 'technology'],
            ['title' => 'UI/UX Designer Intern', 'company' => 'GoTo Group', 'salary_min' => 3500000, 'salary_max' => 4500000, 'type' => 'internship', 'cat' => 'creative'],
            ['title' => 'Data Analyst Junior Officer', 'company' => 'Shopee Indonesia', 'salary_min' => 8000000, 'salary_max' => 12000000, 'type' => 'full_time', 'cat' => 'technology'],
            ['title' => 'Frontend React/Next.js Engineer', 'company' => 'Halodoc', 'salary_min' => 10000000, 'salary_max' => 15000000, 'type' => 'full_time', 'cat' => 'technology'],
            ['title' => 'IT Support & Helpdesk Specialist', 'company' => 'Bank BCA', 'salary_min' => 5500000, 'salary_max' => 7500000, 'type' => 'full_time', 'cat' => 'technology'],
            ['title' => 'Digital Marketing & Content Creator', 'company' => 'Ruangguru', 'salary_min' => 5000000, 'salary_max' => 8000000, 'type' => 'full_time', 'cat' => 'creative'],
            ['title' => 'Freelance Web WordPress Developer', 'company' => 'Wira Digital Studio', 'salary_min' => 4000000, 'salary_max' => 7000000, 'type' => 'freelance', 'cat' => 'technology'],
            ['title' => 'DevOps Cloud Assistant Engineer', 'company' => 'Amazon Web Services ID', 'salary_min' => 12000000, 'salary_max' => 18000000, 'type' => 'full_time', 'cat' => 'engineering'],
            ['title' => 'Mobile Flutter Developer Intern', 'company' => 'Traveloka', 'salary_min' => 4000000, 'salary_max' => 5000000, 'type' => 'internship', 'cat' => 'technology'],
        ];

        foreach ($jobsData as $job) {
            \App\Models\JobVacancy::firstOrCreate(
                ['title' => $job['title'], 'company_name' => $job['company']],
                [
                    'school_id'    => $school->id,
                    'posted_by'    => $adminUser->id,
                    'description'  => "Dibutuhkan lulusan SMK kompeten untuk posisi {$job['title']} di {$job['company']}. Kesempatan berkarir di lingkungan kerja modern.",
                    'requirements' => "- Lulusan SMK RPL / TKJ / sederajat\n- Memiliki portofolio relevan\n- Mampu bekerja dalam tim\n- Komunikatif dan proaktif",
                    'location'     => 'Jakarta (Hybrid / WFO)',
                    'salary_min'   => $job['salary_min'],
                    'salary_max'   => $job['salary_max'],
                    'job_type'     => $job['type'],
                    'category'     => $job['cat'],
                    'deadline'     => now()->addDays(rand(14, 60)),
                    'is_active'    => true,
                ]
            );
        }
        $this->command->info("💼 10 Lowongan Kerja & Karir Alumni dibuat.");

        // ── 12. Alumni Events (5 Agenda & Kegiatan Alumni) ─────────────────────
        $eventsData = [
            ['title' => 'Reuni Akbar Lintas Angkatan SMK Negeri 1 2026', 'date' => now()->addDays(30)->setTime(9, 0), 'loc' => 'Grand Ballroom Hotel Nusantara, Jakarta'],
            ['title' => 'Webinar Career in Tech: Menembus Top Tech Companies', 'date' => now()->addDays(10)->setTime(13, 30), 'loc' => 'Online via Zoom Meeting'],
            ['title' => 'Workshop CV, Resume & Mock Interview Eksklusif', 'date' => now()->addDays(18)->setTime(10, 0), 'loc' => 'Aula Utama SMK Negeri 1'],
            ['title' => 'Job Fair & Campus Hiring Khusus Lulusan SMK 2026', 'date' => now()->addDays(45)->setTime(8, 0), 'loc' => 'Hall Pameran Digital Presensi'],
            ['title' => 'Sharing Session: Dari SMK Menjadi Tech Entrepreneur', 'date' => now()->addDays(7)->setTime(15, 0), 'loc' => 'Ruang Auditorium Lantai 3'],
        ];

        foreach ($eventsData as $ev) {
            \App\Models\AlumniEvent::firstOrCreate(
                ['title' => $ev['title']],
                [
                    'school_id'       => $school->id,
                    'posted_by'       => $adminUser->id,
                    'description'     => "Bergabunglah dalam acara {$ev['title']}. Kesempatan emas untuk networking, menambah wawasan karir, dan mempererat silaturahmi antar alumni.",
                    'event_date'      => $ev['date'],
                    'location'        => $ev['loc'],
                    'is_active'       => true,
                    'approval_status' => 'approved',
                ]
            );
        }
        $this->command->info("🎉 5 Agenda & Event Alumni dibuat.");

        $this->command->newLine();
        $this->command->info('=== 🌟 DATA DUMMY REAL-TIME BERHASIL DIBUAT 🌟 ===');
        $this->command->info('Akun Login Siap Pakai (Password semua akun: password):');
        $this->command->info('  👑 Super Admin : superadmin@simpad.app');
        $this->command->info('  🛡️  Admin       : admin@smkn1demo.sch.id');
        $this->command->info('  👨‍🏫 Guru Utama  : guru@smkn1demo.sch.id');
        $this->command->info('  👨‍🎓 Siswa Demo  : siswa@smkn1demo.sch.id');
        $this->command->info('  🎓 Alumni Demo : ahmad.fauzi2024@alumni.test');
    }
}
