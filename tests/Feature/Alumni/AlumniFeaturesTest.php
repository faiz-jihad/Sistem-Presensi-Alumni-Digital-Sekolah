<?php

use App\Models\School;
use App\Models\Alumni;
use App\Models\User;
use App\Models\AlumniEvent;
use App\Models\JobVacancy;
use App\Mail\AlumniEventApprovedMail;
use App\Mail\AlumniEventRejectedMail;
use App\Mail\JobVacancyApprovedMail;
use App\Exports\AlumniExport;
use App\Services\ExportService;
use App\Models\Export;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

uses(RefreshDatabase::class);

test('mengirim email ketika alumni diverifikasi admin', function () {
    Mail::fake();

    $school = School::create([
        'name' => 'SMK Negeri 1 Jakarta',
        'npsn' => '12345678',
        'status' => 'active',
    ]);
    $user = User::create([
        'name' => 'Alumni User',
        'email' => 'akun-alumni@example.com',
        'password' => bcrypt('password'),
        'role' => 'alumni',
        'school_id' => $school->id,
        'status' => 'active',
    ]);
    $admin = User::create([
        'name' => 'Admin Sekolah',
        'email' => 'admin@example.com',
        'password' => bcrypt('password'),
        'role' => 'admin',
        'school_id' => $school->id,
        'status' => 'active',
    ]);
    $alumni = Alumni::create([
        'school_id' => $school->id,
        'user_id' => $user->id,
        'nisn' => '1000000001',
        'name' => 'Alumni User',
        'gender' => 'male',
        'graduation_year' => 2026,
        'class_name' => 'XII RPL 1',
        'email' => 'alumni@example.com',
        'verification_status' => 'pending',
    ]);

    app(\App\Services\AlumniVerificationService::class)
        ->approveAlumni($alumni, $admin);

    Mail::assertSent(\App\Mail\AlumniAccountVerifiedMail::class, function ($mail) use ($alumni) {
        return $mail->hasTo('alumni@example.com') && $mail->alumni->is($alumni);
    });
});

test('menggunakan email akun ketika email alumni kosong', function () {
    Mail::fake();

    $school = School::create([
        'name' => 'SMK Negeri 2 Jakarta',
        'npsn' => '87654321',
        'status' => 'active',
    ]);
    $user = User::create([
        'name' => 'Alumni Kedua',
        'email' => 'fallback@example.com',
        'password' => bcrypt('password'),
        'role' => 'alumni',
        'school_id' => $school->id,
        'status' => 'active',
    ]);
    $alumni = Alumni::create([
        'school_id' => $school->id,
        'user_id' => $user->id,
        'nisn' => '1000000002',
        'name' => 'Alumni Kedua',
        'gender' => 'female',
        'graduation_year' => 2026,
        'class_name' => 'XII RPL 2',
        'verification_status' => 'pending',
    ]);

    $alumni->update([
        'verification_status' => 'verified',
        'verified_at' => now(),
    ]);

    Mail::assertSent(\App\Mail\AlumniAccountVerifiedMail::class, function ($mail) use ($alumni) {
        return $mail->hasTo('fallback@example.com') && $mail->alumni->is($alumni);
    });
});

test('mengirim email ketika pengajuan kegiatan alumni disetujui', function () {
    Mail::fake();

    $school = School::create(['name' => 'SMK Negeri 1 Jakarta', 'npsn' => '12345678', 'status' => 'active']);
    $user = User::create([
        'name' => 'Alumni User',
        'email' => 'alumni@example.com',
        'password' => bcrypt('password'),
        'role' => 'alumni',
        'school_id' => $school->id,
        'status' => 'active',
    ]);

    $event = AlumniEvent::create([
        'school_id' => $school->id,
        'posted_by' => $user->id,
        'title' => 'Reuni Perak Angkatan 2000',
        'description' => 'Reuni seru',
        'event_date' => now()->addDays(10),
        'location' => 'Aula Sekolah',
        'approval_status' => 'pending',
    ]);

    // Update status ke approved untuk mentrigger observer
    $event->update(['approval_status' => 'approved']);

    Mail::assertSent(AlumniEventApprovedMail::class, function ($mail) use ($user, $event) {
        return $mail->hasTo($user->email) && $mail->event->id === $event->id;
    });
});

test('mengirim email ketika pengajuan kegiatan alumni ditolak', function () {
    Mail::fake();

    $school = School::create(['name' => 'SMK Negeri 1 Jakarta', 'npsn' => '12345678', 'status' => 'active']);
    $user = User::create([
        'name' => 'Alumni User',
        'email' => 'alumni@example.com',
        'password' => bcrypt('password'),
        'role' => 'alumni',
        'school_id' => $school->id,
        'status' => 'active',
    ]);

    $event = AlumniEvent::create([
        'school_id' => $school->id,
        'posted_by' => $user->id,
        'title' => 'Kegiatan Dilarang',
        'description' => 'Kegiatan tidak sesuai visi',
        'event_date' => now()->addDays(5),
        'location' => 'Aula Sekolah',
        'approval_status' => 'pending',
    ]);

    // Update status ke rejected untuk mentrigger observer
    $event->update(['approval_status' => 'rejected']);

    Mail::assertSent(AlumniEventRejectedMail::class, function ($mail) use ($user, $event) {
        return $mail->hasTo($user->email) && $mail->event->id === $event->id;
    });
});

test('mengirim email ketika lowongan kerja disetujui/diaktifkan', function () {
    Mail::fake();

    $school = School::create(['name' => 'SMK Negeri 1 Jakarta', 'npsn' => '12345678', 'status' => 'active']);
    $user = User::create([
        'name' => 'Alumni User',
        'email' => 'alumni@example.com',
        'password' => bcrypt('password'),
        'role' => 'alumni',
        'school_id' => $school->id,
        'status' => 'active',
    ]);

    $job = JobVacancy::create([
        'school_id' => $school->id,
        'posted_by' => $user->id,
        'title' => 'Software Engineer',
        'company_name' => 'Tech Corp',
        'description' => 'Coding',
        'requirements' => 'S1 Teknik Informatika',
        'location' => 'Jakarta',
        'is_active' => false,
    ]);

    // Update status ke active untuk mentrigger observer
    $job->update(['is_active' => true]);

    Mail::assertSent(JobVacancyApprovedMail::class, function ($mail) use ($user, $job) {
        return $mail->hasTo($user->email) && $mail->job->id === $job->id;
    });
});

test('laporan alumni excel dapat diekspor dengan kop sekolah', function () {
    Excel::fake();
    Storage::fake('public');

    $school = School::create([
        'name' => 'SMK Negeri 1 Jakarta',
        'address' => 'Jl. Budi Utomo No. 3',
        'phone' => '021-123456',
        'npsn' => '12345678',
        'status' => 'active'
    ]);

    $export = Export::create([
        'school_id' => $school->id,
        'type' => 'alumni_report',
        'file_type' => 'xlsx',
        'status' => 'pending',
        'file_name' => 'pending_generation',
        'file_path' => 'pending_generation',
        'filters' => [
            'graduation_year' => '2026',
            'verification_status' => 'verified'
        ],
        'created_by' => 1
    ]);

    // Jalankan export service
    $service = app(ExportService::class);
    $service->generate($export);

    // Pastikan status export berhasil
    $export->refresh();
    expect($export->status)->toBe('completed');

    Excel::assertStored($export->file_path, function (AlumniExport $exportClass) use ($school) {
        // Cek properti di export class
        // Periksa apakah data kop sekolah tersimpan dengan benar di class export
        $reflector = new ReflectionClass($exportClass);
        
        $schoolNameProp = $reflector->getProperty('schoolName');
        $schoolNameProp->setAccessible(true);
        
        $schoolAddressProp = $reflector->getProperty('schoolAddress');
        $schoolAddressProp->setAccessible(true);
        
        return $schoolNameProp->getValue($exportClass) === $school->name &&
               $schoolAddressProp->getValue($exportClass) === $school->address;
    });
});
