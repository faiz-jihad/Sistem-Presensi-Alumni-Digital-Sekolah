<?php

namespace App\Filament\Resources\PresensiSessions\Tables;

use App\Enums\DayOfWeek;
use App\Enums\SessionStatus;
use App\Services\PresensiSessionService;
use App\Filament\Resources\StudentAttendances\Pages\ManualAttendance;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;


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
                    ->color(fn ($state): string => match ($state instanceof \App\Enums\SessionStatus ? $state->value : $state) {
                        'scheduled' => 'info',
                        'open'      => 'success',
                        'closed'    => 'gray',
                        'cancelled' => 'danger',
                        default     => 'gray',
                    })
                    ->formatStateUsing(fn ($state): string => $state instanceof \App\Enums\SessionStatus ? $state->label() : match ($state) {
                        'scheduled' => 'Terjadwal',
                        'open'      => 'Dibuka',
                        'closed'    => 'Ditutup',
                        'cancelled' => 'Dibatalkan',
                        default     => (string) $state,
                    }),

                \Filament\Tables\Columns\IconColumn::make('is_late')
                    ->label('Terlambat')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('latitude')
                    ->label('Lokasi Masuk')
                    ->formatStateUsing(fn ($record) => $record->latitude ? "{$record->latitude}, {$record->longitude}" : '-')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('closed_latitude')
                    ->label('Lokasi Keluar')
                    ->formatStateUsing(fn ($record) => $record->closed_latitude ? "{$record->closed_latitude}, {$record->closed_longitude}" : '-')
                    ->toggleable(isToggledHiddenByDefault: true),

                \Filament\Tables\Columns\ImageColumn::make('photo')
                    ->label('Foto Selfie')
                    ->disk('public')
                    ->circular()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('material_topic')
                    ->label('Topik Materi')
                    ->limit(35)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('openedBy.name')
                    ->label('Dibuka Oleh')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('opened_at')
                    ->label('Waktu Buka')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('closed_at')
                    ->label('Waktu Tutup')
                    ->dateTime('d M Y H:i')
                    ->sortable()
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
                    ->options(SessionStatus::options()),

                SelectFilter::make('teacher')
                    ->label('Guru')
                    ->relationship('teacher', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('class')
                    ->label('Kelas')
                    ->relationship('schedule.class', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('subject')
                    ->label('Mata Pelajaran')
                    ->relationship('schedule.subject', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('day')
                    ->label('Hari')
                    ->options(DayOfWeek::options())
                    ->query(fn ($query, array $data) =>
                        $data['value']
                            ? $query->whereHas('schedule', fn ($q) => $q->where('day', $data['value']))
                            : $query
                    ),
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
                        try {
                            app(PresensiSessionService::class)->open($record);

                            Notification::make()
                                ->title('Sesi Presensi Dibuka')
                                ->body("Sesi tanggal {$record->date} berhasil dibuka.")
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Sesi Presensi Gagal Dibuka')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

                Action::make('view_qr')
                    ->label('Lihat QR')
                    ->icon('heroicon-o-qr-code')
                    ->color('info')
                    ->visible(fn ($record) => app(PresensiSessionService::class)->canShowQr($record))
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
                        try {
                            app(PresensiSessionService::class)->close($record, auth()->id());

                            Notification::make()
                                ->title('Sesi Presensi Ditutup')
                                ->body("Sesi tanggal {$record->date} berhasil ditutup.")
                                ->warning()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Sesi Presensi Gagal Ditutup')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

                EditAction::make()
                    ->label('Edit'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->recordUrl(fn ($record) => ManualAttendance::getUrl(['session_id' => $record->id]));
    }
}
