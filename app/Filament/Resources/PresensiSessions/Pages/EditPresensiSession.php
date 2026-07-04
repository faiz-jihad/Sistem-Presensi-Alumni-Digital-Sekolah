<?php

namespace App\Filament\Resources\PresensiSessions\Pages;

use App\Filament\Resources\PresensiSessions\PresensiSessionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPresensiSession extends EditRecord
{
    protected static string $resource = PresensiSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
