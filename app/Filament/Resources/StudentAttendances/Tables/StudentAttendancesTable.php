<?php

namespace App\Filament\Resources\StudentAttendances\Tables;

use App\Jobs\SendWhatsAppNotification;
use App\Models\Student;
use App\Models\SchoolClass;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
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
                    ->icon('heroicon-m-clock')
                    ->color('success')
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
                        'pending'  => 'Menunggu Verifikasi',
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
                Filter::make('tanggal_hari_ini')
                    ->label('Hari Ini')
                    ->query(fn (Builder $query) => $query->whereDate('date', Carbon::today()))
                    ->default(),
                Filter::make('tanggal')
                    ->label('Rentang Tanggal')
                    ->form([
                        DatePicker::make('dari_tanggal')
                            ->label('Dari Tanggal')
                            ->placeholder('dd/mm/yyyy'),
                        DatePicker::make('sampai_tanggal')
                            ->label('Sampai Tanggal')
                            ->placeholder('dd/mm/yyyy'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari_tanggal'],
                                fn (Builder $q, $date) => $q->whereDate('date', '>=', $date)
                            )
                            ->when(
                                $data['sampai_tanggal'],
                                fn (Builder $q, $date) => $q->whereDate('date', '<=', $date)
                            );
                    }),
                SelectFilter::make('class_id')
                    ->label('Kelas')
                    ->options(function () {
                        $user = Auth::user();
                        $query = SchoolClass::query()->orderBy('name');
                        if ($user->role !== 'super_admin' && $user->school_id) {
                            $query->where('school_id', $user->school_id);
                        }
                        return $query->pluck('name', 'id');
                    })
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
                        'pending'  => 'Menunggu Verifikasi',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                    ]),
            ])
            ->actions([
                EditAction::make()
                    ->label('Edit')
                    ->icon('heroicon-m-pencil-square'),
                Action::make('send_whatsapp_notif')
                    ->label('Kirim WA')
                    ->icon('heroicon-m-chat-bubble-left-right')
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
                        $statusVal = $record->status instanceof \App\Enums\AttendanceStatus ? $record->status->value : $record->status;
                        $statusIndonesian = match ($statusVal) {
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
                    ->icon('heroicon-m-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => in_array($record->verification_status, ['pending', null], true) || empty($record->verification_status))
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
                                $statusVal = $record->status instanceof \App\Enums\AttendanceStatus ? $record->status->value : $record->status;
                                $status = $statusVal === 'permission' ? 'Izin (Disetujui)' : 'Sakit (Disetujui)';
                                $msg = "SIMPAD Info:\n\nYth. Orang Tua/Wali {$student->name},\n\nPengajuan {$status} putra/putri Anda untuk tanggal " . Carbon::parse($record->date)->format('d/m/Y') . " telah disetujui.\n\nTerima kasih.\nSIMPAD";
                                dispatch(new SendWhatsAppNotification($phone, $msg));
                            }

                            \Filament\Notifications\Notification::make()
                                ->title('Presensi Disetujui')
                                ->body("Pengajuan kehadiran {$student->name} berhasil disetujui.")
                                ->success()
                                ->send()
                                ->sendToDatabase(Auth::user());
                        }
                    }),
                Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-m-x-circle')
                    ->color('danger')
                    ->visible(fn ($record) => in_array($record->verification_status, ['pending', null], true) || empty($record->verification_status))
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update([
                            'status'              => 'absent',
                            'verification_status' => 'rejected',
                            'verified_by'         => Auth::id(),
                            'verified_at'         => Carbon::now(),
                        ]);

                        \Filament\Notifications\Notification::make()
                            ->title('Presensi Ditolak')
                            ->body("Pengajuan kehadiran {$record->student?->name} ditolak dan status diubah menjadi Alpha.")
                            ->danger()
                            ->send()
                            ->sendToDatabase(Auth::user());
                    }),
            ])
            ->headerActions([
                Action::make('verify_all_class_today')
                    ->label('1 Klik Verifikasi Semua (Per Kelas)')
                    ->icon('heroicon-m-bolt')
                    ->color('success')
                    ->form([
                        \Filament\Forms\Components\Select::make('class_id')
                            ->label('Pilih Kelas')
                            ->options(function () {
                                $user = auth()->user();
                                $query = \App\Models\SchoolClass::query()->orderBy('name');
                                if ($user->role !== 'super_admin' && $user->school_id) {
                                    $query->where('school_id', $user->school_id);
                                }
                                return $query->pluck('name', 'id');
                            })
                            ->required()
                            ->searchable()
                            ->preload(),
                        \Filament\Forms\Components\DatePicker::make('date')
                            ->label('Tanggal')
                            ->default(now())
                            ->required(),
                    ])
                    ->modalHeading('Verifikasi Presensi 1 Klik Per Kelas')
                    ->modalDescription('Semua siswa yang berstatus Hadir/Terlambat/Izin/Sakit pada kelas dan tanggal yang dipilih akan langsung diverifikasi dan disetujui secara otomatis dalam 1 kali klik!')
                    ->modalSubmitActionLabel('⚡ Verifikasi Sekarang')
                    ->action(function (array $data) {
                        $dateStr = \Carbon\Carbon::parse($data['date'])->toDateString();
                        
                        $records = \App\Models\StudentAttendance::with(['student.parent'])
                            ->where('class_id', $data['class_id'])
                            ->whereDate('date', $dateStr)
                            ->get();

                        $count = 0;
                        $updatedCount = 0;
                        foreach ($records as $record) {
                            if ($record->verification_status !== 'approved') {
                                $record->update([
                                    'verification_status' => 'approved',
                                    'verified_by'         => Auth::id(),
                                    'verified_at'         => Carbon::now(),
                                ]);
                                $updatedCount++;

                                $statusVal = $record->status instanceof \App\Enums\AttendanceStatus ? $record->status->value : $record->status;
                                if (in_array($statusVal, ['permission', 'sick'])) {
                                    $phone = $record->student?->parent_phone ?? optional($record->student?->parent)->phone;
                                    if ($phone) {
                                        $status = $statusVal === 'permission' ? 'Izin (Disetujui)' : 'Sakit (Disetujui)';
                                        $msg = "SIMPAD Info:\n\nYth. Orang Tua/Wali {$record->student->name},\n\nPengajuan {$status} putra/putri Anda untuk tanggal " . Carbon::parse($record->date)->format('d/m/Y') . " telah disetujui.\n\nTerima kasih.\nSIMPAD";
                                        dispatch(new SendWhatsAppNotification($phone, $msg));
                                    }
                                }
                            }
                            $count++;
                        }

                        $className = \App\Models\SchoolClass::find($data['class_id'])?->name ?? 'Kelas';
                        
                        if ($count > 0) {
                            $bodyMsg = "Total {$count} data presensi siswa kelas **{$className}** berhasil diverifikasi dan disetujui (Baru diverifikasi: {$updatedCount}).";
                        } else {
                            $bodyMsg = "Tidak ditemukan data presensi sama sekali untuk kelas **{$className}** pada tanggal tersebut di database.";
                        }

                        \Filament\Notifications\Notification::make()
                            ->title('Verifikasi 1 Klik Berhasil!')
                            ->body($bodyMsg)
                            ->success()
                            ->send()
                            ->sendToDatabase(Auth::user());
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->icon(null),
                    \Filament\Actions\BulkAction::make('verify_bulk')
                        ->label('Verifikasi Semua Terpilih (1 Klik)')
                        ->icon('heroicon-m-bolt')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Verifikasi Presensi Terpilih')
                        ->modalDescription('Setujui dan verifikasi semua data presensi yang dipilih dalam 1 kali klik?')
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            $count = 0;
                            foreach ($records as $record) {
                                if ($record->verification_status === 'pending' || !$record->verification_status) {
                                    $record->update([
                                        'verification_status' => 'approved',
                                        'verified_by'         => Auth::id(),
                                        'verified_at'         => Carbon::now(),
                                    ]);
                                    $count++;
                                }
                            }
                            \Filament\Notifications\Notification::make()
                                ->title('Verifikasi Berhasil!')
                                ->body("{$count} data presensi berhasil diverifikasi dalam 1 klik.")
                                ->success()
                                ->send()
                                ->sendToDatabase(Auth::user());
                        }),
                ]),
            ]);
    }
}
