<?php

namespace App\Filament\Resources\Students\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class StudentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nis')
                    ->label('NIS')
                    ->searchable()
                    ->copyable()
                    ->fontFamily('mono'),
                TextColumn::make('name')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold'),
                TextColumn::make('class.name')
                    ->label('Kelas')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color('primary'),
                TextColumn::make('gender')
                    ->label('L/P')
                    ->badge()
                    ->color(fn ($state) => $state === 'male' ? 'info' : 'danger')
                    ->formatStateUsing(fn ($state) => $state === 'male' ? 'L' : 'P'),
                TextColumn::make('parent.name')
                    ->label('Orang Tua / Wali')
                    ->searchable(),
                TextColumn::make('parent.phone')
                    ->label('No. WA Orang Tua')
                    ->searchable()
                    ->copyable()
                    ->placeholder('— Belum diisi —')
                    ->icon('heroicon-o-phone')
                    ->iconColor(fn ($state) => $state ? 'success' : 'gray'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        'graduated' => 'primary',
                        'transferred' => 'warning',
                        'dropout' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'active' => 'Aktif',
                        'inactive' => 'Tidak Aktif',
                        'graduated' => 'Lulus',
                        'transferred' => 'Pindahan',
                        'dropout' => 'Keluar',
                        default => $state,
                    }),
                TextColumn::make('birth_date')
                    ->label('Tanggal Lahir')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('school.name')
                    ->label('Sekolah')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Filter Status')
                    ->options([
                        'active' => 'Aktif',
                        'inactive' => 'Tidak Aktif',
                        'graduated' => 'Lulus',
                        'transferred' => 'Pindahan',
                        'dropout' => 'Keluar',
                    ]),
                TrashedFilter::make()->label('Keranjang Sampah'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
