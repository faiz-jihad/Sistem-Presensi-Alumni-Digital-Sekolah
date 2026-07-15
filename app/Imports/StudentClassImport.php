<?php

namespace App\Imports;

use App\Models\AcademicYear;
use App\Models\StudentClass;
use App\Models\Teacher;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\Importable;

class StudentClassImport implements ToCollection, WithHeadingRow, SkipsOnError
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
            $name = trim($row['nama_kelas_'] ?? $row['nama_kelas'] ?? '');
            $grade = trim($row['tingkat_kelas_1_13'] ?? $row['tingkat'] ?? '');
            $ayName = trim($row['tahun_ajaran_'] ?? $row['tahun_ajaran'] ?? '');
            $major = trim($row['jurusan_peminatan'] ?? $row['jurusan'] ?? '');
            $nip = trim($row['nip_wali_kelas'] ?? '');
            $capacity = isset($row['kapasitas_siswa']) ? (int) $row['kapasitas_siswa'] : (isset($row['kapasitas']) ? (int) $row['kapasitas'] : 36);
            $roomNumber = trim($row['nama_nomor_ruang_kelas'] ?? $row['ruang'] ?? '');
            $status = strtolower(trim($row['status_activeinactive'] ?? $row['status'] ?? 'active'));

            if (empty($name) || empty($grade)) {
                $this->skippedCount++;
                continue;
            }

            // Lewati jika kelas dengan nama yang sama sudah ada di sekolah ini
            if (StudentClass::where('name', $name)->where('school_id', $this->schoolId)->exists()) {
                $this->skippedCount++;
                continue;
            }

            // Resolve Academic Year
            $ayId = null;
            if ($ayName) {
                $ay = AcademicYear::where('name', $ayName)
                    ->where('school_id', $this->schoolId)
                    ->first();
                $ayId = $ay?->id;
            }

            // Fallback ke tahun ajaran aktif jika tidak diisi/tidak ditemukan
            if (!$ayId) {
                $ay = AcademicYear::where('school_id', $this->schoolId)
                    ->where('is_active', true)
                    ->first();
                $ayId = $ay?->id;
            }

            // Jika masih belum ada tahun ajaran, lewati
            if (!$ayId) {
                $this->skippedCount++;
                continue;
            }

            // Resolve Wali Kelas
            $homeroomTeacherId = null;
            if ($nip) {
                $teacher = Teacher::where('nip', $nip)
                    ->where('school_id', $this->schoolId)
                    ->first();
                $homeroomTeacherId = $teacher?->id;
            }

            StudentClass::create([
                'school_id' => $this->schoolId,
                'academic_year_id' => $ayId,
                'name' => $name,
                'grade' => (string) $grade,
                'major' => $major ?: null,
                'homeroom_teacher_id' => $homeroomTeacherId,
                'capacity' => $capacity > 0 ? $capacity : 36,
                'room_number' => $roomNumber ?: null,
                'status' => in_array($status, ['active', 'inactive']) ? $status : 'active',
            ]);

            $this->importedCount++;
        }
    }

    public function headingRow(): int
    {
        return 1;
    }

    public function getImportedCount(): int { return $this->importedCount; }
    public function getSkippedCount(): int { return $this->skippedCount; }
}
