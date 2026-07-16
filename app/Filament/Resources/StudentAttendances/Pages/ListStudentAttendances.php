<?php

namespace App\Filament\Resources\StudentAttendances\Pages;

use App\Filament\Resources\StudentAttendances\StudentAttendanceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListStudentAttendances extends ListRecords
{
    protected static string $resource = StudentAttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('manual_attendance')
                ->label('Presensi Siswa (Per Kelas)')
                ->icon('heroicon-o-pencil-square')
                ->color('success')
                ->url(fn () => StudentAttendanceResource::getUrl('manual'))
                ->visible(fn () => auth()->user()->role !== 'teacher'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\StatsOverview::class,
        ];
    }
}
