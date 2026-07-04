<?php

namespace App\Filament\Resources\StudentAttendances\Pages;

use App\Filament\Resources\StudentAttendances\StudentAttendanceResource;
use Filament\Resources\Pages\CreateRecord;

class CreateStudentAttendance extends CreateRecord
{
    protected static string $resource = StudentAttendanceResource::class;
}
