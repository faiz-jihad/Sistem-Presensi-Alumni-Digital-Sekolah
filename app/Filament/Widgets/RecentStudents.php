<?php

namespace App\Filament\Widgets;

use App\Models\Student;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class RecentStudents extends BaseWidget
{
    protected static ?int $sort = 8;

    public static function canView(): bool
    {
        return in_array(auth()->user()->role, ['super_admin', 'admin', 'teacher']);
    }

    protected int | string | array $columnSpan = [
        'default' => 12,
        'md'      => 6,
        'lg'      => 4,
    ];

    protected static ?string $heading = 'Siswa Terbaru';

    protected function getTableQuery(): Builder
    {
        return Student::query()->latest()->limit(5);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('nis')
                    ->label('NIS')
                    ->badge()
                    ->color('primary'),
                TextColumn::make('created_at')
                    ->label('Ditambahkan')
                    ->dateTime('d M Y')
                    ->color('gray'),
            ])
            ->paginated(false);
    }
}
