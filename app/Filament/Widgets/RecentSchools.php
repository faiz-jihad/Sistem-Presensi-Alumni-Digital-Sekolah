<?php

namespace App\Filament\Widgets;

use App\Models\School;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class RecentSchools extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = [
        'default' => 12,
        'md'      => 6,
        'lg'      => 4,
    ];

    protected static ?string $heading = 'Sekolah Terbaru';

    protected function getTableQuery(): Builder
    {
        return School::query()->latest()->limit(5);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Sekolah')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('created_at')
                    ->label('Ditambahkan')
                    ->dateTime('d M Y')
                    ->color('gray'),
            ])
            ->paginated(false);
    }
}
