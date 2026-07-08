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
                TextColumn::make('approval_status')
                    ->label('Status Persetujuan')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Menunggu Persetujuan',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                        default => $state,
                    }),
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
                Action::make('approve_event')
                    ->label('Setujui')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => in_array(auth()->user()->role, ['super_admin', 'admin']) && $record->approval_status === 'pending')
                    ->action(function ($record) {
                        $record->update(['approval_status' => 'approved']);
                        Notification::make()->title('Kegiatan disetujui')->success()->send();
                    }),
                Action::make('reject_event')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => in_array(auth()->user()->role, ['super_admin', 'admin']) && $record->approval_status === 'pending')
                    ->action(function ($record) {
                        $record->update(['approval_status' => 'rejected']);
                        Notification::make()->title('Kegiatan ditolak')->danger()->send();
                    }),
                Action::make('toggle')
                    ->label(fn($record) => $record->is_active ? 'Nonaktifkan' : 'Aktifkan')
                    ->icon('heroicon-o-check-circle')
                    ->requiresConfirmation()
                    ->action(function($record){
                        $record->update([
                            'is_active' => !$record->is_active
                        ]);

                        Notification::make()
                            ->title('Status kegiatan berhasil diubah')
                            ->success()
                            ->send();
                    }),
                EditAction::make()
                    ->label('Edit'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
