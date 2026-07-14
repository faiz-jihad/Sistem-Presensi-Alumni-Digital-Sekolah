<?php

namespace App\Filament\Resources\Schools\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SchoolsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                    
                TextColumn::make('npsn')
                    ->label('NPSN')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('level')
                    ->label('Jenjang')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'sd' => 'info',
                        'smp' => 'success',
                        'sma' => 'warning',
                        'smk' => 'primary',
                        'ma' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'sd' => 'SD',
                        'smp' => 'SMP',
                        'sma' => 'SMA',
                        'smk' => 'SMK',
                        'ma' => 'MA',
                        default => $state,
                    }),
                    
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
                    }),
                    
                TextColumn::make('principal_name')
                    ->label('Kepala Sekolah')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                TextColumn::make('phone')
                    ->label('Telepon')
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                TextColumn::make('email')
                    ->label('Email')
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('level')
                    ->label('Jenjang')
                    ->options([
                        'sd' => 'SD',
                        'smp' => 'SMP',
                        'sma' => 'SMA',
                        'smk' => 'SMK',
                        'ma' => 'MA',
                    ])
                    ->placeholder('Semua Jenjang'),

                SelectFilter::make('status')
                    ->label('Status Aktif')
                    ->options([
                        'active' => 'Aktif',
                        'inactive' => 'Tidak Aktif',
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
            ->actions([
                EditAction::make()
                    ->label('Edit')
                    ->icon('heroicon-m-pencil-square')
                    ->color('warning')
                    ->slideOver(),
                    
                DeleteAction::make()
                    ->label('Hapus')
                    ->icon('heroicon-m-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Hapus Sekolah?')
                    ->modalDescription('Apakah Anda yakin ingin menghapus sekolah ini?'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Hapus Terpilih')
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Data Terpilih?'),
                ]),
            ])
            ->defaultSort('name')
            ->emptyStateHeading('Belum ada data sekolah')
            ->emptyStateDescription('Tambahkan sekolah pertama Anda untuk memulai.')
            ->emptyStateIcon('heroicon-o-building-office');
    }
}