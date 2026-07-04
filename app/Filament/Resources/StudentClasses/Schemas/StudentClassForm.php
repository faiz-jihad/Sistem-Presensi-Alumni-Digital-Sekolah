<?php

namespace App\Filament\Resources\StudentClasses\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class StudentClassForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('school_id')
                    ->label('Sekolah')
                    ->relationship('school', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('academic_year_id')
                    ->label('Tahun Akademik')
                    ->relationship('academicYear', 'name')
                    ->searchable()
                    ->preload(),
                TextInput::make('name')
                    ->label('Nama Kelas')
                    ->placeholder('Contoh: XII RPL 1')
                    ->required(),
                Select::make('grade')
                    ->label('Tingkat / Kelas')
                    ->options(['10' => '10', '11' => '11', '12' => '12', '13' => '13'])
                    ->native(false)
                    ->required(),
                TextInput::make('major')
                    ->label('Jurusan')
                    ->placeholder('Contoh: RPL'),
                Select::make('homeroom_teacher_id')
                    ->label('Wali Kelas')
                    ->relationship('homeroomTeacher', 'name')
                    ->searchable()
                    ->preload(),
                TextInput::make('capacity')
                    ->label('Kapasitas')
                    ->required()
                    ->numeric()
                    ->default(30),
                TextInput::make('room_number')
                    ->label('Nomor Ruangan')
                    ->placeholder('Contoh: Ruang 102'),
                Select::make('status')
                    ->label('Status')
                    ->options(['active' => 'Aktif', 'inactive' => 'Tidak Aktif'])
                    ->default('active')
                    ->native(false)
                    ->required(),
            ]);
    }
}
