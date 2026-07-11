<?php

namespace App\Filament\Resources\Semesters\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SemestersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('academicYear.school.name')
                    ->label('Sekolah')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('academicYear.name')
                    ->label('Tahun Ajaran')
                    ->sortable(),

                TextColumn::make('name')
                    ->label('Semester')
                    ->badge()
                    ->color(fn ($state) => $state === 'Ganjil' ? 'primary' : 'success'),

                TextColumn::make('type')
                    ->label('Jenis')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'odd' => 'Ganjil',
                        'even' => 'Genap',
                    })
                    ->color(fn (string $state) => match ($state) {
                        'odd' => 'warning',
                        'even' => 'success',
                    }),

                TextColumn::make('start_date')
                    ->label('Mulai')
                    ->date('d M Y'),

                TextColumn::make('end_date')
                    ->label('Selesai')
                    ->date('d M Y'),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('school_id')
                    ->label('Sekolah')
                    ->relationship('academicYear.school', 'name')
                    ->placeholder('Semua Sekolah')
                    ->preload()
                    ->searchable()
                    ->visible(fn () => auth()->user()->isSuperAdmin()),

                SelectFilter::make('academic_year_id')
                    ->label('Tahun Ajaran')
                    ->relationship('academicYear', 'name')
                    ->placeholder('Semua Tahun Ajaran')
                    ->preload()
                    ->searchable(),

                SelectFilter::make('type')
                    ->label('Jenis Semester')
                    ->options([
                        'odd' => 'Ganjil',
                        'even' => 'Genap',
                    ])
                    ->placeholder('Semua Jenis'),

                SelectFilter::make('is_active')
                    ->label('Status Aktif')
                    ->options([
                        '1' => 'Aktif',
                        '0' => 'Tidak Aktif',
                    ])
                    ->placeholder('Semua Status'),
            ])
            ->filtersFormColumns(2)
            ->filtersTriggerAction(fn ($action) => $action
                ->button()
                ->label('Filter')
                ->icon('heroicon-m-funnel')
                ->color('gray')
            )
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