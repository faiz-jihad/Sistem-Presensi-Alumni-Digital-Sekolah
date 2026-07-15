<?php

namespace App\Imports;

use App\Models\School;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\Importable;

class TeacherImport implements ToCollection, WithHeadingRow, SkipsOnError
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
            $nip   = substr(preg_replace('/[^0-9]/', '', trim($row['nip'] ?? '')), 0, 18);
            $name  = trim($row['nama_lengkap_guru'] ?? $row['nama'] ?? '');
            $email = trim($row['email'] ?? '');
            $password = trim($row['kata_sandi'] ?? $row['password'] ?? 'password123');
            $gender = strtolower(trim($row['jenis_kelamin'] ?? ''));
            $gender = in_array($gender, ['laki-laki', 'male', 'l']) ? 'male' : 'female';
            $phone = trim($row['no_telepon'] ?? '');
            $fieldOfStudy = trim($row['mata_pelajaran_utama'] ?? '');
            $employmentStatus = strtolower(trim($row['status_kepegawaian'] ?? 'honorer'));
            $status = strtolower(trim($row['status'] ?? 'active'));
            $joinDate = $row['tanggal_mulai_bertugas'] ?? null;
            $educationLevel = trim($row['tingkat_pendidikan'] ?? '');
            $university = trim($row['universitas'] ?? '');

            if (empty($nip) || empty($name) || empty($email)) {
                $this->skippedCount++;
                continue;
            }

            // Lewati jika NIP sudah ada
            if (Teacher::where('nip', $nip)->where('school_id', $this->schoolId)->exists()) {
                $this->skippedCount++;
                continue;
            }

            // Buat atau ambil user
            $user = User::where('email', $email)->first();
            if (!$user && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $user = User::create([
                    'name'      => $name,
                    'email'     => $email,
                    'password'  => Hash::make($password ?: 'password123'),
                    'role'      => 'teacher',
                    'school_id' => $this->schoolId,
                    'status'    => 'active',
                ]);
                if (method_exists($user, 'assignRole')) {
                    try { $user->assignRole('teacher'); } catch (\Throwable) {}
                }
            }

            // Validasi joinDate
            $parsedJoinDate = null;
            if ($joinDate) {
                try {
                    $parsedJoinDate = \Carbon\Carbon::parse($joinDate)->toDateString();
                } catch (\Throwable) {
                    $parsedJoinDate = null;
                }
            }

            Teacher::create([
                'school_id'         => $this->schoolId,
                'user_id'           => $user?->id,
                'nip'               => $nip,
                'name'              => $name,
                'gender'            => $gender,
                'phone'             => $phone ?: null,
                'field_of_study'    => $fieldOfStudy ?: null,
                'employment_status' => in_array($employmentStatus, ['pns', 'pppk', 'honorer', 'gtt', 'ptt', 'kontrak']) ? $employmentStatus : 'honorer',
                'status'            => in_array($status, ['active', 'inactive', 'retired']) ? $status : 'active',
                'join_date'         => $parsedJoinDate,
                'education_level'   => $educationLevel ?: null,
                'university'        => $university ?: null,
            ]);

            $this->importedCount++;
        }
    }

    public function getImportedCount(): int { return $this->importedCount; }
    public function getSkippedCount(): int { return $this->skippedCount; }
}
