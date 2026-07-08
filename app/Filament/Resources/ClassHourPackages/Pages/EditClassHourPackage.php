<?php

namespace App\Filament\Resources\ClassHourPackages\Pages;

use App\Filament\Resources\ClassHourPackages\ClassHourPackageResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditClassHourPackage extends EditRecord
{
    protected static string $resource = ClassHourPackageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
