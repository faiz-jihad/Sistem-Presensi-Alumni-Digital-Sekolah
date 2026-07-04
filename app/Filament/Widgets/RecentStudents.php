<?php

namespace App\Filament\Widgets;

use App\Models\Student;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;

class RecentStudents extends BaseWidget
{
    protected function getTableQuery(): Builder
    {
        return Student::query()->latest()->limit(5);
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('name')
                ->label('Nama Siswa')
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
