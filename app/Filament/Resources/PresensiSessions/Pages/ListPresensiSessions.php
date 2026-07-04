<?php

namespace App\Filament\Resources\PresensiSessions\Pages;

use App\Filament\Resources\PresensiSessions\PresensiSessionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPresensiSessions extends ListRecords
{
    protected static string $resource = PresensiSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
