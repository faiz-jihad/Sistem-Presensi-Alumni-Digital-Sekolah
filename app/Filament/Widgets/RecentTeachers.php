<?php

namespace App\Filament\Widgets;

use App\Models\Teacher;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class RecentTeachers extends BaseWidget
{
    protected static ?int $sort = 5;

    protected int | string | array $columnSpan = [
        'default' => 12,
        'md'      => 6,
        'lg'      => 4,
    ];

    protected static ?string $heading = 'Guru Terbaru';

    protected function getTableQuery(): Builder
    {
        return Teacher::query()->latest()->limit(5);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Guru')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('nip')
                    ->label('NIP')
                    ->badge()
                    ->color('success'),
                TextColumn::make('created_at')
                    ->label('Ditambahkan')
                    ->dateTime('d M Y')
                    ->color('gray'),
            ])
            ->paginated(false);
    }
}
