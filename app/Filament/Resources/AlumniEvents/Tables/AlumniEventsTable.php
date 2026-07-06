<?php

namespace App\Filament\Resources\AlumniEvents\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Notifications\Notification;
use Filament\Actions\Action;

class AlumniEventsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('school.name')
                    ->label('Sekolah')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('postedBy.name')
                    ->label('Dibuat Oleh')
                    ->searchable(),
                TextColumn::make('title')
                    ->label('Judul Kegiatan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('event_date')
                    ->label('Waktu Pelaksanaan')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                TextColumn::make('location')
                    ->label('Lokasi')
                    ->searchable(),
                ImageColumn::make('banner_image')
                    ->label('Banner')
                    ->disk('public')
                    ->circular(),
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('is_active')
                    ->label('Status')
                    ->options([
                        '1' => 'Aktif',
                        '0' => 'Tidak Aktif',
                    ]),
            ])
            ->recordActions([
                Action::make('toggle')
                    ->label(fn($record) => $record->is_active ? 'Nonaktifkan' : 'Aktifkan')
                    ->icon('heroicon-o-check-circle')
                    ->requiresConfirmation()
                    ->action(function($record){
                        $record->update([
                            'is_active' => !$record->is_active
                        ]);

                        Notification::make()
                            ->title('Status event berhasil diubah')
                            ->success()
                            ->send();
                    }),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
