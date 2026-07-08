<?php

namespace App\Filament\Resources\Schedules\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SchedulesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('class.name')
                    ->label('Kelas')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('subject.name')
                    ->label('Mata Pelajaran')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('teacher.name')
                    ->label('Guru Pengampu')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('classHourPackage.name')
                    ->label('Paket Jam')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('classHour')
                    ->label('Jam Pelajaran')
                    ->formatStateUsing(function ($record) {
                        if (!$record->classHour) return '-';
                        return "Jam Ke-{$record->classHour->order} ({$record->classHour->code})";
                    })
                    ->sortable(),
                TextColumn::make('day')
                    ->label('Hari')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state?->value ?? $state) {
                        'monday' => 'Senin',
                        'tuesday' => 'Selasa',
                        'wednesday' => 'Rabu',
                        'thursday' => 'Kamis',
                        'friday' => 'Jumat',
                        'saturday' => 'Sabtu',
                        'sunday' => 'Minggu',
                        default => $state,
                    })
                    ->sortable(),
                TextColumn::make('room')
                    ->label('Ruangan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('school.name')
                    ->label('Sekolah')
                    ->searchable()
                    ->sortable()
                    ->visible(fn () => auth()->user()->role === 'super_admin'),
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
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
