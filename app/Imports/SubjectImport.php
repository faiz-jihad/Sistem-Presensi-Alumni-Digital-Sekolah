<?php

namespace App\Imports;

use App\Models\Subject;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\Importable;

class SubjectImport implements ToCollection, WithHeadingRow, SkipsOnError
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
            $code        = trim($row['kode_mapel_'] ?? $row['kode_mapel'] ?? '');
            $name        = trim($row['nama_mata_pelajaran_'] ?? $row['nama_mata_pelajaran'] ?? $row['nama'] ?? '');
            $shortName   = trim($row['singkatan'] ?? '');
            $group       = strtolower(trim($row['kelompok_general_specialized_local_extracurricular'] ?? $row['kelompok'] ?? 'general'));
            $creditHours = (int) ($row['beban_jam_jp_'] ?? $row['beban_jam_jp'] ?? 2);
            $status      = strtolower(trim($row['status_active_inactive'] ?? $row['status'] ?? 'active'));
            $description = trim($row['deskripsi'] ?? '');

            if (empty($code) || empty($name)) {
                $this->skippedCount++;
                continue;
            }

            // Lewati jika kode + school_id sudah ada
            if (Subject::where('code', $code)->where('school_id', $this->schoolId)->exists()) {
                $this->skippedCount++;
                continue;
            }

            $allowedGroups = ['general', 'specialized', 'local', 'extracurricular'];

            Subject::create([
                'school_id'    => $this->schoolId,
                'code'         => $code,
                'name'         => $name,
                'short_name'   => $shortName ?: null,
                'group'        => in_array($group, $allowedGroups) ? $group : 'general',
                'credit_hours' => $creditHours > 0 ? $creditHours : 2,
                'status'       => in_array($status, ['active', 'inactive']) ? $status : 'active',
                'description'  => $description ?: null,
            ]);

            $this->importedCount++;
        }
    }

    public function headingRow(): int
    {
        return 4;
    }

    public function getImportedCount(): int { return $this->importedCount; }
    public function getSkippedCount(): int { return $this->skippedCount; }
}
