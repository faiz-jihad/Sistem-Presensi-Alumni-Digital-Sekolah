<?php

namespace App\Filament\Resources\AlumniResource\Pages;

use App\Filament\Resources\AlumniResource\AlumniResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewAlumni extends ViewRecord
{
    protected static string $resource = AlumniResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->label('Edit'),
            DeleteAction::make()
                ->label('Hapus'),
            Action::make('back')
                ->label('Kembali')
                ->url(fn () => AlumniResource::getUrl('index'))
                ->color('gray'),
        ];
    }
}