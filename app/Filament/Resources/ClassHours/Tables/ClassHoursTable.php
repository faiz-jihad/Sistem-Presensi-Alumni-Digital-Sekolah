<?php

namespace App\Filament\Resources\ClassHours\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ClassHoursTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('school.name')
                    ->label('Sekolah')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('code')
                    ->label('Kode')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('start_time')
                    ->label('Jam Mulai')
                    ->time('H:i')
                    ->sortable(),
                TextColumn::make('end_time')
                    ->label('Jam Selesai')
                    ->time('H:i')
                    ->sortable(),
                TextColumn::make('duration_minutes')
                    ->label('Durasi (Menit)')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('order')
                    ->label('Urutan Ke-')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_break')
                    ->label('Istirahat')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('shift')
                    ->label('Shift')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'morning' => 'success',
                        'afternoon' => 'warning',
                        'evening' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'morning' => 'Pagi',
                        'afternoon' => 'Siang',
                        'evening' => 'Malam',
                        default => $state,
                    })
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Aktif',
                        'inactive' => 'Tidak Aktif',
                        default => $state,
                    })
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
