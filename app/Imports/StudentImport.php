<?php

namespace App\Imports;

use App\Models\School;
use App\Models\Student;
use App\Models\StudentClass;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\Importable;

class StudentImport implements ToCollection, WithHeadingRow, SkipsOnError
{
    use Importable, SkipsErrors;

    private int $importedCount = 0;
    private int $skippedCount = 0;

    public function __construct(
        private int $schoolId
    ) {}

    public function collection(Collection $rows): void
    {
        foreach ($rows as $row) {
            // Normalisasi key (lowercase + ganti spasi/strip dengan underscore)
            $nis    = str_replace(' ', '', trim($row['nis'] ?? ''));
            $nisn   = substr(preg_replace('/[^0-9]/', '', trim($row['nisn'] ?? '')), 0, 10);
            $name   = trim($row['nama_lengkap_siswa'] ?? $row['nama'] ?? '');
            $gender = strtolower(trim($row['jenis_kelamin'] ?? ''));
            $gender = in_array($gender, ['laki-laki', 'male', 'l']) ? 'male' : 'female';
            $birthDate = $row['tanggal_lahir'] ?? null;
            $status = strtolower(trim($row['status'] ?? 'active'));
            $email  = trim($row['email'] ?? '');
            $password = trim($row['kata_sandi'] ?? $row['password'] ?? '');
            $className = trim($row['kelas'] ?? '');
            $parentName = trim($row['nama_orang_tua'] ?? '');
            $parentPhone = trim($row['no_wa_orang_tua'] ?? '');

            if (empty($nis) || empty($name)) {
                $this->skippedCount++;
                continue;
            }

            // Lewati jika NIS sudah ada
            if (Student::where('nis', $nis)->where('school_id', $this->schoolId)->exists()) {
                $this->skippedCount++;
                continue;
            }

            // Resolve class by name
            $classId = null;
            if ($className) {
                $class = StudentClass::where('name', $className)
                    ->where('school_id', $this->schoolId)
                    ->first();
                $classId = $class?->id;
            }

            // Validasi birthdate
            $parsedDate = null;
            if ($birthDate) {
                try {
                    $parsedDate = \Carbon\Carbon::parse($birthDate)->toDateString();
                } catch (\Throwable) {
                    $parsedDate = null;
                }
            }

            // Buat student
            $student = Student::create([
                'school_id'  => $this->schoolId,
                'class_id'   => $classId,
                'nis'        => $nis,
                'nisn'       => $nisn ?: null,
                'name'       => $name,
                'gender'     => $gender,
                'birth_date' => $parsedDate,
                'status'     => in_array($status, ['active', 'inactive', 'graduated', 'transferred', 'dropout']) ? $status : 'active',
                'parent_phone' => $parentPhone ?: null,
            ]);

            // Buat user akun siswa jika ada email
            if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                if (! User::where('email', $email)->exists()) {
                    $user = User::create([
                        'name'      => $name,
                        'email'     => $email,
                        'password'  => Hash::make($password ?: 'password123'),
                        'role'      => 'student',
                        'school_id' => $this->schoolId,
                        'status'    => 'active',
                    ]);
                    // Assign Spatie role
                    if (method_exists($user, 'assignRole')) {
                        try { $user->assignRole('student'); } catch (\Throwable) {}
                    }
                }
            }

            $this->importedCount++;
        }
    }

    public function getImportedCount(): int { return $this->importedCount; }
    public function getSkippedCount(): int { return $this->skippedCount; }
}
