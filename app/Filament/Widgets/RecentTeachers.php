<?php

namespace App\Filament\Widgets;

use App\Models\Teacher;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;

class RecentTeachers extends BaseWidget
{
    protected function getTableQuery(): Builder
    {
        return Teacher::query()->latest()->limit(5);
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('name')
                ->label('Nama Guru')
                ->searchable()
                ->sortable(),
            TextColumn::make('email')
                ->label('Email')
                ->searchable(),
            TextColumn::make('created_at')
                ->label('Ditambahkan')
                ->dateTime(),
        ];
    }
}
