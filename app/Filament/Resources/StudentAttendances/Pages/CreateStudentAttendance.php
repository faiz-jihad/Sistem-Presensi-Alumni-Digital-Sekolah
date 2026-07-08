<?php

namespace App\Filament\Resources\StudentAttendances\Pages;

use App\Filament\Resources\StudentAttendances\StudentAttendanceResource;
use Filament\Resources\Pages\CreateRecord;

class CreateStudentAttendance extends CreateRecord
{
    protected static string $resource = StudentAttendanceResource::class;

    public function getTitle(): string
    {
        return 'Tambah Presensi Siswa';
    }

    public function getBreadcrumb(): string
    {
        return 'Tambah';
    }

    protected function getCreateFormAction(): \Filament\Actions\Action
    {
        return parent::getCreateFormAction()
            ->label('Simpan');
    }

    protected function getCreateAnotherFormAction(): \Filament\Actions\Action
    {
        return parent::getCreateAnotherFormAction()
            ->label('Simpan & Tambah Lagi');
    }

    protected function getCancelFormAction(): \Filament\Actions\Action
    {
        return parent::getCancelFormAction()
            ->label('Kembali');
    }    

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
