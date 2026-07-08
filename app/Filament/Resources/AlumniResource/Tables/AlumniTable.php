<?php

namespace App\Filament\Resources\AlumniResource\Tables;

use App\Filament\Resources\AlumniResource\AlumniResource;
use App\Models\Alumni;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class AlumniTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable() // Sudah benar
                    ->sortable(),
                Tables\Columns\TextColumn::make('nisn')
                    ->label('NISN')
                    ->searchable() // Sudah benar
                    ->sortable(), // Tambahkan sortable untuk NISN
                Tables\Columns\TextColumn::make('school.name')
                    ->label('Sekolah')
                    ->searchable() // Sudah benar
                    ->sortable(),
                Tables\Columns\TextColumn::make('class_name')
                    ->label('Kelas Lulus')
                    ->searchable() // Sudah benar
                    ->sortable(), // Tambahkan sortable
                Tables\Columns\TextColumn::make('graduation_year')
                    ->label('Tahun Lulus')
                    ->sortable(),
                    // ->searchable(), // Bisa ditambahkan jika ingin searchable
                Tables\Columns\TextColumn::make('gender')
                    ->label('Jenis Kelamin')
                    ->formatStateUsing(fn (string $state): string => $state === 'male' ? 'Laki-laki' : 'Perempuan')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'male' ? 'primary' : 'success')
                    ->searchable(), // Tambahkan searchable untuk gender
                Tables\Columns\TextColumn::make('profile.current_status')
                    ->label('Status Saat Ini')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'studying' => 'Kuliah',
                        'working' => 'Bekerja',
                        'entrepreneur' => 'Wirausaha',
                        'studying_working' => 'Kuliah & Kerja',
                        'unemployed' => 'Belum Bekerja',
                        default => '-',
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'studying' => 'info',
                        'working' => 'success',
                        'entrepreneur' => 'warning',
                        'studying_working' => 'primary',
                        'unemployed' => 'danger',
                        default => 'gray',
                    })
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('verification_status')
                    ->label('Status Verifikasi')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Menunggu Verifikasi',
                        'verified' => 'Terverifikasi',
                        'rejected' => 'Ditolak',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'verified' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->searchable(), // Tambahkan searchable untuk status
                Tables\Columns\TextColumn::make('verified_at')
                    ->label('Tanggal Verifikasi')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            // TAMBAHKAN: Konfigurasi search secara global
            ->filters([
                Tables\Filters\SelectFilter::make('school_id')
                    ->label('Sekolah')
                    ->relationship('school', 'name'),
                Tables\Filters\SelectFilter::make('verification_status')
                    ->label('Status Verifikasi')
                    ->options([
                        'pending' => 'Menunggu Verifikasi',
                        'verified' => 'Terverifikasi',
                        'rejected' => 'Ditolak',
                    ]),
                Tables\Filters\SelectFilter::make('gender')
                    ->label('Jenis Kelamin')
                    ->options([
                        'male' => 'Laki-laki',
                        'female' => 'Perempuan',
                    ]),
                Tables\Filters\Filter::make('graduation_year')
                    ->label('Tahun Lulus')
                    ->form([
                        Select::make('year')
                            ->label('Pilih Tahun')
                            ->options(array_combine(
                                range(date('Y'), 2000),
                                range(date('Y'), 2000)
                            ))
                            ->placeholder('Semua Tahun'),
                    ])
                    ->query(function ($query, $data) {
                        if (!empty($data['year'])) {
                            $query->where('graduation_year', $data['year']);
                        }
                    }),
                Tables\Filters\SelectFilter::make('profile.current_status')
                    ->label('Status Saat Ini')
                    ->options([
                        'studying' => 'Kuliah',
                        'working' => 'Bekerja',
                        'entrepreneur' => 'Wirausaha',
                        'studying_working' => 'Kuliah & Kerja',
                        'unemployed' => 'Belum Bekerja',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['value'])) {
                            $query->whereHas('profile', function (Builder $query) use ($data) {
                                $query->where('current_status', $data['value']);
                            });
                        }
                    }),
            ])
            ->searchable()
            ->searchPlaceholder('Cari alumni...')
            ->searchDebounce(300)
            ->actions([
                Action::make('edit')
                    ->label('Edit')
                    ->icon('heroicon-o-pencil-square')
                    ->url(fn (Alumni $record): string => AlumniResource::getUrl('edit', ['record' => $record])),
                
                Action::make('delete')
                    ->label('Hapus')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn (Alumni $record) => $record->delete()),
                
                Action::make('verify')
                    ->label('Verifikasi')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(fn (Alumni $record) => $record->verification_status === 'pending')
                    ->requiresConfirmation()
                    ->modalHeading('Verifikasi Alumni?')
                    ->modalDescription('Alumni ini akan diverifikasi dan mendapatkan notifikasi persetujuan.')
                    ->action(function (Alumni $record) {
                        $record->update([
                            'verification_status' => 'verified',
                            'verified_by'         => Auth::id(),
                            'verified_at'         => now(),
                            'verification_notes'  => null,
                        ]);

                        // Notifikasi ke user alumni di panel
                        if ($record->user) {
                            Notification::make()
                                ->title('Akun Anda Telah Diverifikasi ✅')
                                ->body("Selamat! Data alumni Anda telah disetujui. Anda kini dapat mengakses semua fitur alumni.")
                                ->success()
                                ->sendToDatabase($record->user);
                        }

                        Notification::make()
                            ->title('Alumni berhasil diverifikasi')
                            ->success()
                            ->send();
                    }),
                
                Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Alumni $record) => $record->verification_status === 'pending')
                    ->requiresConfirmation()
                    ->modalHeading('Tolak Pendaftaran Alumni?')
                    ->modalDescription('Alumni akan menerima notifikasi penolakan beserta alasannya.')
                    ->form([
                        Textarea::make('verification_notes')
                            ->label('Alasan Penolakan')
                            ->required()
                            ->placeholder('Masukkan alasan penolakan data alumni...'),
                    ])
                    ->action(function (Alumni $record, array $data) {
                        $record->update([
                            'verification_status' => 'rejected',
                            'verified_by'         => Auth::id(),
                            'verified_at'         => now(),
                            'verification_notes'  => $data['verification_notes'],
                        ]);

                        // Notifikasi ke user alumni di panel
                        if ($record->user) {
                            Notification::make()
                                ->title('Pendaftaran Alumni Ditolak ❌')
                                ->body("Maaf, data Anda ditolak. Alasan: {$data['verification_notes']}. Silakan hubungi admin sekolah.")
                                ->danger()
                                ->sendToDatabase($record->user);
                        }

                        Notification::make()
                            ->title('Data alumni berhasil ditolak')
                            ->danger()
                            ->send();
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('delete')
                        ->label('Hapus Terpilih')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->delete()),
                    
                    BulkAction::make('verify_selected')
                        ->label('Verifikasi Terpilih')
                        ->icon('heroicon-o-check-badge')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $count = 0;
                            foreach ($records as $record) {
                                if ($record->verification_status === 'pending') {
                                    $record->update([
                                        'verification_status' => 'verified',
                                        'verified_by' => Auth::id(),
                                        'verified_at' => now(),
                                    ]);
                                    $count++;
                                }
                            }
                            
                            Notification::make()
                                ->title($count . ' alumni berhasil diverifikasi')
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->emptyStateHeading('Belum ada data alumni')
            ->emptyStateDescription('Klik tombol "Tambah Alumni" untuk menambahkan data alumni pertama')
            ->emptyStateIcon('heroicon-o-users')
            ->emptyStateActions([
                Action::make('create')
                    ->label('Tambah Alumni')
                    ->icon('heroicon-o-plus')
                    ->color('primary')
                    ->url(fn () => AlumniResource::getUrl('create')),
            ])
            ->defaultSort('created_at', 'desc');
    }
}