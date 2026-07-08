<?php

namespace App\Filament\Resources\StudentClasses\Pages;

use App\Filament\Resources\StudentClasses\StudentClassResource;
use Filament\Resources\Pages\CreateRecord;

class CreateStudentClass extends CreateRecord
{
    protected static string $resource = StudentClassResource::class;

    public function getTitle(): string
    {
        return 'Tambah Kelas';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
