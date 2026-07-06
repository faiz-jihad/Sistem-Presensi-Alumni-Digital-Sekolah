<?php

namespace App\Filament\Resources\AlumniResource\Pages;

use App\Filament\Resources\AlumniResource\AlumniResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAlumni extends EditRecord
{
    protected static string $resource = AlumniResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('Hapus'),
            Action::make('back')
                ->label('Kembali')
                ->url(fn () => AlumniResource::getUrl('index'))
                ->color('gray'),
        ];
    }
}