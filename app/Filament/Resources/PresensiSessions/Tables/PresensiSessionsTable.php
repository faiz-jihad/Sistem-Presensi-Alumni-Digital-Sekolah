<?php

namespace App\Filament\Resources\PresensiSessions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Carbon\Carbon;

class PresensiSessionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('date')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('teacher.name')
                    ->label('Guru')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('schedule.class.name')
                    ->label('Kelas')
                    ->sortable()
                    ->badge()
                    ->color('primary'),

                TextColumn::make('schedule.subject.name')
                    ->label('Mata Pelajaran')
                    ->sortable(),

                TextColumn::make('start_time')
                    ->label('Jam Mulai')
                    ->time('H:i'),

                TextColumn::make('end_time')
                    ->label('Jam Selesai')
                    ->time('H:i'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'scheduled' => 'info',
                        'open'      => 'success',
                        'closed'    => 'gray',
                        'cancelled' => 'danger',
                        default     => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'scheduled' => 'Terjadwal',
                        'open'      => 'Dibuka',
                        'closed'    => 'Ditutup',
                        'cancelled' => 'Dibatalkan',
                        default     => $state,
                    }),

                TextColumn::make('material_topic')
                    ->label('Topik Materi')
                    ->limit(35)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('date', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Status Sesi')
                    ->options([
                        'scheduled' => 'Terjadwal',
                        'open'      => 'Dibuka',
                        'closed'    => 'Ditutup',
                        'cancelled' => 'Dibatalkan',
                    ]),
            ])
            ->recordActions([
                Action::make('open_session')
                    ->label('Buka Sesi')
                    ->icon('heroicon-o-play-circle')
                    ->color('success')
                    ->visible(fn ($record) => in_array($record->status, ['scheduled']))
                    ->requiresConfirmation()
                    ->modalHeading('Buka Sesi Presensi')
                    ->modalDescription('Sesi presensi akan dibuka dan siswa dapat melakukan scan QR.')
                    ->modalSubmitActionLabel('Ya, Buka Sekarang')
                    ->action(function ($record) {
                        $record->update([
                            'status'     => 'open',
                            'start_time' => $record->start_time ?? Carbon::now()->format('H:i:s'),
                        ]);
                        Notification::make()
                            ->title('Sesi Presensi Dibuka')
                            ->body("Sesi tanggal {$record->date} berhasil dibuka.")
                            ->success()
                            ->send();
                    }),

                Action::make('view_qr')
                    ->label('Lihat QR')
                    ->icon('heroicon-o-qr-code')
                    ->color('info')
                    ->visible(fn ($record) => $record->status === 'open')
                    ->url(fn ($record) => \App\Filament\Resources\PresensiSessions\PresensiSessionResource::getUrl('qr', ['record' => $record->id]))
                    ->openUrlInNewTab(),

                Action::make('close_session')
                    ->label('Tutup Sesi')
                    ->icon('heroicon-o-stop-circle')
                    ->color('warning')
                    ->visible(fn ($record) => $record->status === 'open')
                    ->requiresConfirmation()
                    ->modalHeading('Tutup Sesi Presensi')
                    ->modalDescription('Siswa tidak akan bisa melakukan scan QR setelah sesi ditutup.')
                    ->modalSubmitActionLabel('Ya, Tutup Sesi')
                    ->action(function ($record) {
                        $record->update([
                            'status'    => 'closed',
                            'end_time'  => $record->end_time ?? Carbon::now()->format('H:i:s'),
                            'closed_by' => auth()->id(),
                            'closed_at' => Carbon::now(),
                        ]);
                        Notification::make()
                            ->title('Sesi Presensi Ditutup')
                            ->body("Sesi tanggal {$record->date} berhasil ditutup.")
                            ->warning()
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
