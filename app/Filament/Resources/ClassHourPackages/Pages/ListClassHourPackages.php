<?php

namespace App\Filament\Resources\ClassHourPackages\Pages;

use App\Filament\Resources\ClassHourPackages\ClassHourPackageResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListClassHourPackages extends ListRecords
{
    protected static string $resource = ClassHourPackageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Paket Jam Belajar')
                ->icon('heroicon-o-plus'),
        ];
    }
}
