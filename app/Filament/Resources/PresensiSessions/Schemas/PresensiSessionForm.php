<?php

namespace App\Filament\Resources\PresensiSessions\Schemas;

use App\Models\Schedule;
use App\Models\School;
use App\Models\Teacher;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PresensiSessionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Sesi')
                    ->description('Isi data sesi presensi yang akan dibuka.')
                    ->icon('heroicon-o-queue-list')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('school_id')
                                ->label('Sekolah')
                                ->relationship('school', 'name')
                                ->required()
                                ->searchable()
                                ->preload(),

                            Select::make('schedule_id')
                                ->label('Jadwal Pelajaran')
                                ->options(function () {
                                    return Schedule::query()
                                        ->with(['class', 'subject', 'teacher'])
                                        ->orderBy('day')
                                        ->get()
                                        ->mapWithKeys(fn($schedule) => [
                                            $schedule->id => trim(sprintf('%s - %s - %s', $schedule->class?->name ?? '-', $schedule->subject?->name ?? '-', $schedule->teacher?->name ?? '-')),
                                        ])
                                        ->toArray();
                                })
                                ->required()
                                ->searchable()
                                ->preload(),
                        ]),

                        Grid::make(2)->schema([
                            Select::make('teacher_id')
                                ->label('Guru Pengampu')
                                ->relationship('teacher', 'name')
                                ->required()
                                ->searchable()
                                ->preload(),
                        ]),

                        Grid::make(2)->schema([
                            DatePicker::make('date')
                                ->label('Tanggal Sesi')
                                ->required()
                                ->default(now()),

                            Select::make('status')
                                ->label('Status Sesi')
                                ->options(\App\Enums\SessionStatus::options())
                                ->required()
                                ->default('scheduled'),
                        ]),

                        Grid::make(2)->schema([
                            TimePicker::make('start_time')
                                ->label('Jam Mulai')
                                ->seconds(false),

                            TimePicker::make('end_time')
                                ->label('Jam Selesai')
                                ->seconds(false),
                        ]),
                    ]),

                Section::make('Detail Materi & Catatan')
                    ->icon('heroicon-o-document-text')
                    ->collapsed()
                    ->schema([
                        Textarea::make('material_topic')
                            ->label('Topik Materi')
                            ->placeholder('Contoh: Bab 3 - Sistem Pernapasan')
                            ->rows(2)
                            ->columnSpanFull(),

                        Textarea::make('notes')
                            ->label('Catatan Sesi')
                            ->placeholder('Catatan tambahan tentang sesi ini...')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
