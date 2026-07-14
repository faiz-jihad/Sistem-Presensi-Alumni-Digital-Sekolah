<?php

namespace App\Filament\Resources\Teachers\Pages;

use App\Exports\TeacherImportTemplateExport;
use App\Filament\Resources\Teachers\TeacherResource;
use App\Imports\TeacherImport;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

class ListTeachers extends ListRecords
{
    protected static string $resource = TeacherResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('download_template')
                ->label('Unduh Template')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->action(function () {
                    return Excel::download(
                        new TeacherImportTemplateExport(),
                        'template-import-guru.xlsx'
                    );
                }),

            Action::make('import_teachers')
                ->label('Impor Excel')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('warning')
                ->form([
                    FileUpload::make('file')
                        ->label('File Excel Guru')
                        ->acceptedFileTypes([
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'application/vnd.ms-excel',
                        ])
                        ->disk('local')
                        ->directory('imports/teachers')
                        ->required()
                        ->helperText('Unggah berkas .xlsx sesuai format template. Kolom wajib: NIP, Nama Lengkap, Email, Status.'),
                ])
                ->action(function (array $data) {
                    $user = auth()->user();
                    $schoolId = $user->school_id;

                    if (!$schoolId) {
                        Notification::make()
                            ->title('Sekolah tidak ditemukan. Pastikan akun Anda terhubung ke sekolah.')
                            ->warning()
                            ->send();
                        return;
                    }

                    try {
                        $importer = new TeacherImport($schoolId);
                        $filePath = \Illuminate\Support\Facades\Storage::disk('local')->path($data['file']);
                        Excel::import($importer, $filePath);

                        Notification::make()
                            ->title("Impor selesai: {$importer->getImportedCount()} guru berhasil ditambahkan, {$importer->getSkippedCount()} baris dilewati.")
                            ->success()
                            ->send();
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->title('Impor gagal: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            CreateAction::make()
                ->label('Tambah Guru')
                ->icon('heroicon-o-plus'),
        ];
    }
}
