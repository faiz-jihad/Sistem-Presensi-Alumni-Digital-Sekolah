<?php

namespace App\Filament\Resources\Students\Pages;

use App\Exports\StudentImportTemplateExport;
use App\Filament\Resources\Students\StudentResource;
use App\Imports\StudentImport;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

class ListStudents extends ListRecords
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Tombol Unduh Template
            Action::make('download_template')
                ->label('Unduh Template')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->action(function () {
                    return Excel::download(
                        new StudentImportTemplateExport(),
                        'template-import-siswa.xlsx'
                    );
                }),

            // Tombol Import
            Action::make('import_students')
                ->label('Impor Excel')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('warning')
                ->form(function () {
                    $fields = [];
                    if (auth()->user()->isSuperAdmin()) {
                        $fields[] = \Filament\Forms\Components\Select::make('school_id')
                            ->label('Pilih Sekolah')
                            ->options(\App\Models\School::pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->required();
                    }
                    $fields[] = FileUpload::make('file')
                        ->label('File Excel Siswa')
                        ->acceptedFileTypes([
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'application/vnd.ms-excel',
                        ])
                        ->disk('local')
                        ->directory('imports/students')
                        ->required()
                        ->helperText('Unggah berkas .xlsx sesuai format template. Kolom wajib: NIS, Nama Lengkap, Jenis Kelamin, Tanggal Lahir, Status.');
                    return $fields;
                })
                ->action(function (array $data) {
                    $user = auth()->user();
                    $schoolId = $user->isSuperAdmin() ? ($data['school_id'] ?? null) : $user->school_id;

                    if (!$schoolId) {
                        Notification::make()
                            ->title('Gagal: Sekolah harus dipilih')
                            ->danger()
                            ->send();
                        return;
                    }

                    try {
                        $importer = new StudentImport($schoolId);
                        $filePath = \Illuminate\Support\Facades\Storage::disk('local')->path($data['file']);
                        Excel::import($importer, $filePath);

                        Notification::make()
                            ->title("Impor selesai: {$importer->getImportedCount()} siswa berhasil ditambahkan, {$importer->getSkippedCount()} baris dilewati.")
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
                ->label('Tambah Siswa')
                ->icon('heroicon-o-plus'),
        ];
    }
}
