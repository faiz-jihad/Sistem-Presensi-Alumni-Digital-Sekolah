<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                TextColumn::make('role')
                    ->label('Peran')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'super_admin' => 'Super Admin',
                        'admin' => 'Admin',
                        'teacher' => 'Guru',
                        'student' => 'Siswa',
                        'parent' => 'Orang Tua / Wali',
                        'alumni' => 'Alumni',
                        default => $state,
                    }),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'active' => 'success',
                        'suspended' => 'danger',
                        default => 'warning',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'active' => 'Aktif',
                        'inactive' => 'Tidak Aktif',
                        'suspended' => 'Ditangguhkan',
                        default => $state,
                    }),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->label('Peran')
                    ->options([
                        'super_admin' => 'Super Admin',
                        'admin' => 'Admin',
                        'teacher' => 'Guru',
                        'student' => 'Siswa',
                        'parent' => 'Orang Tua / Wali',
                        'alumni' => 'Alumni',
                    ]),
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Aktif',
                        'inactive' => 'Tidak Aktif',
                        'suspended' => 'Ditangguhkan',
                    ]),
            ])
            ->actions([
                EditAction::make()
                    ->label('Edit'),
                \Filament\Actions\Action::make('send_push')
                    ->label('Kirim Push')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('info')
                    ->form([
                        \Filament\Forms\Components\TextInput::make('title')
                            ->label('Judul Notifikasi')
                            ->required()
                            ->default('Info Sekolah'),
                        \Filament\Forms\Components\Textarea::make('body')
                            ->label('Isi Pesan')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function ($record, array $data) {
                        // 1. Send Filament database notification (bell icon)
                        \Filament\Notifications\Notification::make()
                            ->title($data['title'])
                            ->body($data['body'])
                            ->info()
                            ->sendToDatabase($record);

                        // 2. Send Firebase FCM push notification
                        $service = app(\App\Services\FirebaseNotificationService::class);
                        $result = $service->sendPushNotification($record, $data['title'], $data['body']);
                        
                        // 3. Inform the admin who triggered the action
                        if ($result['success']) {
                            \Filament\Notifications\Notification::make()
                                ->title('Notifikasi Terkirim')
                                ->body("Berhasil disimpan ke database & terkirim ke {$result['success_count']} perangkat.")
                                ->success()
                                ->send();
                        } else {
                            \Filament\Notifications\Notification::make()
                                ->title('Notifikasi Tersimpan')
                                ->body("Berhasil disimpan ke database. Namun gagal mengirim push: " . ($result['message'] ?? 'Tidak ada perangkat terdaftar.'))
                                ->warning()
                                ->send();
                        }
                    }),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('name');
    }
}
