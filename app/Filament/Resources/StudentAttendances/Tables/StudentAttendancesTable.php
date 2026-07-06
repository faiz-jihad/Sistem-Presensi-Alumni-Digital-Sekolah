<?php

namespace App\Filament\Resources\StudentAttendances\Tables;

use App\Jobs\SendWhatsAppNotification;
use App\Models\Student;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class StudentAttendancesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('student.name')
                    ->label('Nama Siswa')
                    ->description(fn ($record) => "NIS: " . ($record->student?->nis ?? '-') . " | NISN: " . ($record->student?->nisn ?? '-'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('class.name')
                    ->label('Kelas')
                    ->badge()
                    ->color('primary')
                    ->sortable(),
                TextColumn::make('date')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state): string => match ($state instanceof \App\Enums\AttendanceStatus ? $state->value : $state) {
                        'present'    => 'success',
                        'late'       => 'warning',
                        'permission' => 'info',
                        'sick'       => 'info',
                        'absent'     => 'danger',
                        default      => 'gray',
                    })
                    ->formatStateUsing(fn ($state): string => $state instanceof \App\Enums\AttendanceStatus ? $state->label() : match ($state) {
                        'present'    => 'Hadir',
                        'late'       => 'Terlambat',
                        'permission' => 'Izin',
                        'sick'       => 'Sakit',
                        'absent'     => 'Alpha',
                        default      => (string) $state,
                    }),
                TextColumn::make('check_in_time')
                    ->label('Jam Masuk')
                    ->icon('heroicon-m-arrow-right-on-rectangle')
                    ->color('success')
                    ->time('H:i')
                    ->placeholder('-'),
                TextColumn::make('check_out_time')
                    ->label('Jam Pulang')
                    ->icon('heroicon-m-arrow-left-on-rectangle')
                    ->color('danger')
                    ->time('H:i')
                    ->placeholder('-'),
                TextColumn::make('verification_status')
                    ->label('Verifikasi')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'pending'  => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default    => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'pending'  => 'Menunggu',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                        null       => '-',
                        default    => $state,
                    }),
                TextColumn::make('teacher.name')
                    ->label('Dicatat Oleh')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('date', 'desc')
            ->filters([
                SelectFilter::make('class_id')
                    ->label('Filter Kelas')
                    ->relationship('class', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('status')
                    ->label('Status Kehadiran')
                    ->options([
                        'present'    => 'Hadir',
                        'late'       => 'Terlambat',
                        'permission' => 'Izin',
                        'sick'       => 'Sakit',
                        'absent'     => 'Alpha',
                    ]),
                SelectFilter::make('verification_status')
                    ->label('Status Verifikasi')
                    ->options([
                        'pending'  => 'Menunggu',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('send_whatsapp_notif')
                    ->label('Kirim WA Orang Tua')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Kirim Notifikasi WhatsApp')
                    ->modalDescription('Kirim ulang rincian kehadiran siswa ini ke WhatsApp orang tua?')
                    ->modalSubmitActionLabel('Ya, Kirim')
                    ->action(function ($record) {
                        $student = Student::with(['parent'])->find($record->student_id);
                        if (!$student) {
                            \Filament\Notifications\Notification::make()
                                ->title('Gagal Mengirim')
                                ->body('Data siswa tidak ditemukan.')
                                ->danger()
                                ->send();
                            return;
                        }

                        $phone = $student->parent_phone ?? optional($student->parent)->phone;
                        if (empty($phone)) {
                            \Filament\Notifications\Notification::make()
                                ->title('Gagal Mengirim')
                                ->body('Nomor telepon orang tua tidak ditemukan.')
                                ->warning()
                                ->send();
                            return;
                        }

                        $dateFormatted = Carbon::parse($record->date)->translatedFormat('d F Y');
                        $statusIndonesian = match ($record->status) {
                            'present' => 'Hadir',
                            'late' => 'Terlambat',
                            'permission' => 'Izin',
                            'sick' => 'Sakit',
                            'absent' => 'Alpha / Tidak Hadir',
                            default => 'Tidak Diketahui',
                        };

                        $message = "SIMPAD Info:\n\nYth. Orang Tua/Wali dari {$student->name},\n\nDiberitahukan bahwa putra/putri Anda tercatat *{$statusIndonesian}* pada tanggal {$dateFormatted}.\n";

                        if ($record->check_in_time) {
                            $message .= "Jam Masuk: {$record->check_in_time}\n";
                        }
                        if ($record->note) {
                            $message .= "Catatan: {$record->note}\n";
                        }

                        $message .= "\nTerima kasih.\nSistem Presensi Sekolah SIMPAD";

                        dispatch(new SendWhatsAppNotification($phone, $message));

                        \Filament\Notifications\Notification::make()
                            ->title('WhatsApp Terkirim')
                            ->body("Pemberitahuan kehadiran {$student->name} berhasil dikirim ke orang tua.")
                            ->success()
                            ->send();
                    }),
                Action::make('approve')
                    ->label('Setujui')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => $record->verification_status === 'pending')
                    ->action(function ($record) {
                        $record->update([
                            'verification_status' => 'approved',
                            'verified_by'         => Auth::id(),
                            'verified_at'         => Carbon::now(),
                        ]);

                        // Kirim notif WA ke orang tua
                        $student = Student::with(['parent'])->find($record->student_id);
                        if ($student) {
                            $phone = $student->parent_phone ?? optional($student->parent)->phone;
                            if ($phone) {
                                $status = $record->status === 'permission' ? 'Izin (Disetujui)' : 'Sakit (Disetujui)';
                                $msg = "SIMPAD Info:\n\nYth. Orang Tua/Wali {$student->name},\n\nPengajuan {$status} putra/putri Anda untuk tanggal " . Carbon::parse($record->date)->format('d/m/Y') . " telah disetujui.\n\nTerima kasih.\nSIMPAD";
                                dispatch(new SendWhatsAppNotification($phone, $msg));
                            }
                        }
                    }),
                Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn ($record) => $record->verification_status === 'pending')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update([
                            'status'              => 'absent',
                            'verification_status' => 'rejected',
                            'verified_by'         => Auth::id(),
                            'verified_at'         => Carbon::now(),
                        ]);
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
