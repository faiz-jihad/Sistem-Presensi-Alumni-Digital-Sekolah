<?php

namespace App\Filament\Widgets;

use App\Models\School;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;

class RecentSchools extends BaseWidget
{
    protected function getTableQuery(): Builder
    {
        return School::query()->latest()->limit(5);
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('name')
                ->label('Nama Sekolah')
                ->searchable()
                ->sortable(),
            TextColumn::make('created_at')
                ->label('Ditambahkan')
                ->dateTime(),
        ];
    }
}
