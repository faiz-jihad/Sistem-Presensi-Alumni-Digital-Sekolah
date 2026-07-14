<?php

use App\Models\School;
use App\Models\StudentClass;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Subject;
use App\Models\User;
use App\Imports\StudentImport;
use App\Imports\TeacherImport;
use App\Imports\SubjectImport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Seed roles & permissions agar Spatie Role tersedia saat import user
    $this->artisan('db:seed', ['--class' => 'RoleAndPermissionSeeder']);
});

test('impor siswa berhasil memproses koleksi baris excel', function () {
    $school = School::create(['name' => 'SMK Negeri 1 Jakarta', 'npsn' => '12345678', 'status' => 'active']);
    
    // Buat kelas terlebih dahulu agar dapat di-resolve saat import
    $class = StudentClass::create([
        'school_id' => $school->id,
        'name' => 'X RPL 1',
        'grade' => '10'
    ]);

    $importData = new Collection([
        [
            'nis' => '10001',
            'nisn' => '1234567890',
            'nama_lengkap_siswa' => 'Ahmad Fajar',
            'jenis_kelamin' => 'Laki-laki',
            'tanggal_lahir' => '2006-08-15',
            'kelas' => 'X RPL 1',
            'status' => 'active',
            'nama_orang_tua' => 'Budi Santoso',
            'no_wa_orang_tua' => '628123456789',
            'email' => 'ahmad.fajar@example.com',
            'password' => 'password123'
        ],
        [
            'nis' => '10002',
            'nisn' => '1234567891',
            'nama_lengkap_siswa' => 'Siti Nurhaliza',
            'jenis_kelamin' => 'Perempuan',
            'tanggal_lahir' => '2007-03-22',
            'kelas' => 'X RPL 1',
            'status' => 'active',
            'nama_orang_tua' => 'Rina Wati',
            'no_wa_orang_tua' => '628987654321',
            'email' => 'siti.nurhaliza@example.com',
            'password' => 'password123'
        ]
    ]);

    $importer = new StudentImport($school->id);
    $importer->collection($importData);

    expect($importer->getImportedCount())->toBe(2);
    expect($importer->getSkippedCount())->toBe(0);

    // Cek di database
    $this->assertDatabaseHas('students', [
        'school_id' => $school->id,
        'class_id' => $class->id,
        'nis' => '10001',
        'name' => 'Ahmad Fajar',
        'gender' => 'male',
        'status' => 'active'
    ]);

    $this->assertDatabaseHas('users', [
        'email' => 'ahmad.fajar@example.com',
        'role' => 'student',
        'school_id' => $school->id
    ]);
});

test('impor guru berhasil memproses koleksi baris excel', function () {
    $school = School::create(['name' => 'SMK Negeri 1 Jakarta', 'npsn' => '12345678', 'status' => 'active']);

    $importData = new Collection([
        [
            'nip' => '198706052010011001',
            'nama_lengkap_guru' => 'Budi Santoso, S.Pd.',
            'email' => 'budi.santoso@example.com',
            'password' => 'password123',
            'jenis_kelamin' => 'Laki-laki',
            'no_telepon' => '081234567890',
            'mata_pelajaran_utama' => 'Matematika',
            'status_kepegawaian' => 'pns',
            'status' => 'active',
            'tanggal_mulai_bertugas' => '2010-01-05',
            'tingkat_pendidikan' => 'S1',
            'universitas' => 'UNJ'
        ]
    ]);

    $importer = new TeacherImport($school->id);
    $importer->collection($importData);

    expect($importer->getImportedCount())->toBe(1);
    expect($importer->getSkippedCount())->toBe(0);

    $this->assertDatabaseHas('teachers', [
        'school_id' => $school->id,
        'nip' => '198706052010011001',
        'name' => 'Budi Santoso, S.Pd.',
        'gender' => 'male',
        'employment_status' => 'pns',
        'status' => 'active'
    ]);

    $this->assertDatabaseHas('users', [
        'email' => 'budi.santoso@example.com',
        'role' => 'teacher',
        'school_id' => $school->id
    ]);
});

test('impor mata pelajaran berhasil memproses koleksi baris excel', function () {
    $school = School::create(['name' => 'SMK Negeri 1 Jakarta', 'npsn' => '12345678', 'status' => 'active']);

    $importData = new Collection([
        [
            'kode_mapel' => 'MTK',
            'nama_mata_pelajaran' => 'Matematika',
            'singkatan' => 'MTK',
            'kelompok' => 'general',
            'beban_jam_jp' => 4,
            'status' => 'active',
            'deskripsi' => 'Mata pelajaran wajib'
        ]
    ]);

    $importer = new SubjectImport($school->id);
    $importer->collection($importData);

    expect($importer->getImportedCount())->toBe(1);
    expect($importer->getSkippedCount())->toBe(0);

    $this->assertDatabaseHas('subjects', [
        'school_id' => $school->id,
        'code' => 'MTK',
        'name' => 'Matematika',
        'short_name' => 'MTK',
        'group' => 'general',
        'credit_hours' => 4,
        'status' => 'active'
    ]);
});
