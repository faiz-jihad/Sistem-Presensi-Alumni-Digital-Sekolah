<?php

namespace App\Filament\Resources\StudentAttendances\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;

class StudentAttendanceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('school_id')
                    ->relationship('school', 'name')
                    ->required()
                    ->searchable(),
                Select::make('class_id')
                    ->relationship('class', 'name')
                    ->required()
                    ->searchable(),
                Select::make('student_id')
                    ->relationship('student', 'name')
                    ->required()
                    ->searchable(),
                Select::make('teacher_id')
                    ->relationship('teacher', 'name')
                    ->nullable()
                    ->searchable(),
                DatePicker::make('date')
                    ->required(),
                TimePicker::make('check_in_time')
                    ->nullable(),
                TimePicker::make('check_out_time')
                    ->nullable(),
                Select::make('status')
                    ->options([
                        'present'    => 'Hadir',
                        'late'       => 'Terlambat',
                        'permission' => 'Izin',
                        'sick'       => 'Sakit',
                        'absent'     => 'Alpha',
                    ])
                    ->required(),
                Textarea::make('note')
                    ->nullable()
                    ->columnSpanFull(),
                Select::make('verification_status')
                    ->options([
                        'pending'  => 'Menunggu',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                    ])
                    ->nullable(),
            ]);
    }
}
