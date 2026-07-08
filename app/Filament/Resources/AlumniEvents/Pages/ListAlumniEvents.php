<?php

namespace App\Filament\Resources\AlumniEvents\Pages;

use App\Filament\Resources\AlumniEvents\AlumniEventResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAlumniEvents extends ListRecords
{
    protected static string $resource = AlumniEventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Kegiatan Alumni')
                ->icon('heroicon-o-plus'),
        ];
    }
}
