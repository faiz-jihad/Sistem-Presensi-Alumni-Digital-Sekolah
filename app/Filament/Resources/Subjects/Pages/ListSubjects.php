<?php

namespace App\Filament\Resources\Subjects\Pages;

use App\Exports\SubjectImportTemplateExport;
use App\Filament\Resources\Subjects\SubjectResource;
use App\Imports\SubjectImport;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

class ListSubjects extends ListRecords
{
    protected static string $resource = SubjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('download_template')
                ->label('Unduh Template')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->action(function () {
                    return Excel::download(
                        new SubjectImportTemplateExport(),
                        'template-import-mata-pelajaran.xlsx'
                    );
                }),

            Action::make('import_subjects')
                ->label('Impor Excel')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('warning')
                ->form([
                    FileUpload::make('file')
                        ->label('File Excel Mata Pelajaran')
                        ->acceptedFileTypes([
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'application/vnd.ms-excel',
                        ])
                        ->disk('local')
                        ->directory('imports/subjects')
                        ->required()
                        ->helperText('Unggah berkas .xlsx sesuai format template. Kolom wajib: Kode Mapel, Nama, Kelompok, Beban Jam, Status.'),
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
                        $importer = new SubjectImport($schoolId);
                        $filePath = \Illuminate\Support\Facades\Storage::disk('local')->path($data['file']);
                        Excel::import($importer, $filePath);

                        Notification::make()
                            ->title("Impor selesai: {$importer->getImportedCount()} mata pelajaran berhasil ditambahkan, {$importer->getSkippedCount()} baris dilewati.")
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
                ->label('Tambah Mata Pelajaran')
                ->icon('heroicon-o-plus'),
        ];
    }
}
