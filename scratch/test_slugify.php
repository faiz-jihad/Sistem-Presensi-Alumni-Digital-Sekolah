<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Exports\StudentImportTemplateExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TestImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        var_dump($rows->first()?->toArray());
    }
}

// Generate the template to a temporary file
$tempFile = tempnam(sys_get_temp_dir(), 'excel');
Excel::store(new StudentImportTemplateExport(), $tempFile, 'local', \Maatwebsite\Excel\Excel::XLSX);

$realPath = \Illuminate\Support\Facades\Storage::disk('local')->path($tempFile);
echo "Reading generated template from: $realPath\n";

Excel::import(new TestImport(), $realPath, 'local');

@unlink($realPath);
