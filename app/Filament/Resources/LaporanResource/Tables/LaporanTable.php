<?php

namespace App\Filament\Resources\LaporanResource\Tables;

use App\Models\Export;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\DeleteAction;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

class LaporanTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('type')
                    ->label('Tipe Laporan')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'attendance_report' => 'Presensi Kehadiran',
                        'alumni_report' => 'Data Alumni',
                        'student_report' => 'Data Siswa',
                        'teacher_report' => 'Data Guru',
                        default => $state,
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'attendance_report' => 'info',
                        'alumni_report' => 'success',
                        'student_report' => 'warning',
                        'teacher_report' => 'gray',
                        default => 'gray',
                    }),

                TextColumn::make('filters')
                    ->label('Filter/Kriteria')
                    ->state(function (Export $record): string {
                        $filters = $record->filters ?? [];
                        if ($record->type === 'alumni_report') {
                            $year = $filters['graduation_year'] ?? 'Semua Tahun';
                            $status = match ($filters['verification_status'] ?? '') {
                                'verified' => 'Terverifikasi',
                                'pending' => 'Menunggu Verifikasi',
                                'rejected' => 'Ditolak',
                                default => 'Semua Status'
                            };
                            return "Alumni - Lulusan: {$year} ({$status})";
                        } elseif ($record->type === 'student_report') {
                            $classId = $filters['class_id'] ?? null;
                            $className = $classId ? (\App\Models\StudentClass::find($classId)?->name ?? 'Kelas Tidak Ditemukan') : 'Semua Kelas';
                            $status = match ($filters['status'] ?? '') {
                                'active' => 'Aktif',
                                'inactive' => 'Tidak Aktif',
                                'graduated' => 'Lulus',
                                default => 'Semua Status'
                            };
                            return "Siswa - Kelas: {$className} ({$status})";
                        } elseif ($record->type === 'teacher_report') {
                            $status = match ($filters['status'] ?? '') {
                                'active' => 'Aktif',
                                'inactive' => 'Tidak Aktif',
                                'retired' => 'Pensiun',
                                default => 'Semua Status'
                            };
                            return "Guru - Status: {$status}";
                        }
                        return '-';
                    }),

                TextColumn::make('file_type')
                    ->label('Format')
                    ->formatStateUsing(fn (string $state): string => strtoupper($state))
                    ->badge()
                    ->color(fn (string $state): string => $state === 'xlsx' ? 'success' : 'danger'),

                TextColumn::make('creator.name')
                    ->label('Dibuat Oleh')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'processing' => 'info',
                        'completed' => 'success',
                        'failed' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Menunggu Verifikasi',
                        'processing' => 'Diproses',
                        'completed' => 'Selesai',
                        'failed' => 'Gagal',
                        default => $state,
                    }),

                TextColumn::make('created_at')
                    ->label('Tanggal Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->actions([
                Action::make('download')
                    ->label('Unduh')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->visible(fn (Export $record): bool => $record->status === 'completed' && !empty($record->file_path))
                    ->action(function (Export $record) {
                        try {
                            // Cek apakah file ada di disk public
                            if (Storage::disk('public')->exists($record->file_path)) {
                                // Download file langsung tanpa redirect
                                return Storage::disk('public')->download($record->file_path);
                            } 
                            // Jika tidak ada, coba cek di disk local
                            else if (Storage::disk('local')->exists($record->file_path)) {
                                return Storage::disk('local')->download($record->file_path);
                            } 
                            else {
                                Notification::make()
                                    ->title('Berkas tidak ditemukan')
                                    ->body('Berkas laporan tidak tersedia di server')
                                    ->danger()
                                    ->send();
                            }
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Gagal mengunduh berkas')
                                ->body('Terjadi kesalahan: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

                DeleteAction::make()
                    ->action(function (Export $record) {
                        // Hapus file fisik juga
                        if (!empty($record->file_path)) {
                            Storage::disk('public')->delete($record->file_path);
                        }
                        $record->delete();
                        
                        Notification::make()
                            ->title('Laporan berhasil dihapus')
                            ->success()
                            ->send();
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }
}