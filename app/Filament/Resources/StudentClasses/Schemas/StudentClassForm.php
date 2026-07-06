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
                    ->required()
                    ->live(), // Membuat field sekolah reaktif
                    
                Select::make('academic_year_id')
                    ->label('Tahun Ajaran & Semester')
                    ->relationship('academicYear', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                    
                TextInput::make('name')
                    ->label('Nama Kelas')
                    ->placeholder('Contoh: XII RPL 1 atau VI A')
                    ->required(),
                    
                Select::make('grade')
                    ->label('Tingkat / Kelas')
                    ->options(function (callable $get) {
                        $schoolId = $get('school_id') ?? auth()->user()?->school_id;
                        if (!$schoolId) {
                            // Fallback jika tidak ada sekolah terpilih
                            return [
                                '1' => 'Tingkat 1', '2' => 'Tingkat 2', '3' => 'Tingkat 3', 
                                '4' => 'Tingkat 4', '5' => 'Tingkat 5', '6' => 'Tingkat 6',
                                '7' => 'Tingkat 7', '8' => 'Tingkat 8', '9' => 'Tingkat 9',
                                '10' => 'Tingkat 10', '11' => 'Tingkat 11', '12' => 'Tingkat 12', 
                                '13' => 'Tingkat 13'
                            ];
                        }

                        $school = \App\Models\School::find($schoolId);
                        if (!$school) {
                            return [];
                        }

                        return match ($school->level) {
                            'sd' => [
                                '1' => 'Tingkat 1 (SD)',
                                '2' => 'Tingkat 2 (SD)',
                                '3' => 'Tingkat 3 (SD)',
                                '4' => 'Tingkat 4 (SD)',
                                '5' => 'Tingkat 5 (SD)',
                                '6' => 'Tingkat 6 (SD)',
                            ],
                            'smp' => [
                                '7' => 'Tingkat 7 (SMP)',
                                '8' => 'Tingkat 8 (SMP)',
                                '9' => 'Tingkat 9 (SMP)',
                            ],
                            'sma', 'ma' => [
                                '10' => 'Tingkat 10 (SMA/MA)',
                                '11' => 'Tingkat 11 (SMA/MA)',
                                '12' => 'Tingkat 12 (SMA/MA)',
                            ],
                            'smk' => [
                                '10' => 'Tingkat 10 (SMK)',
                                '11' => 'Tingkat 11 (SMK)',
                                '12' => 'Tingkat 12 (SMK)',
                                '13' => 'Tingkat 13 (SMK 4 Tahun)',
                            ],
                            default => [
                                '1' => 'Tingkat 1', '2' => 'Tingkat 2', '3' => 'Tingkat 3', 
                                '4' => 'Tingkat 4', '5' => 'Tingkat 5', '6' => 'Tingkat 6',
                                '7' => 'Tingkat 7', '8' => 'Tingkat 8', '9' => 'Tingkat 9',
                                '10' => 'Tingkat 10', '11' => 'Tingkat 11', '12' => 'Tingkat 12', 
                                '13' => 'Tingkat 13'
                            ]
                        };
                    })
                    ->native(false)
                    ->required(),
                    
                TextInput::make('major')
                    ->label('Jurusan / Peminatan')
                    ->placeholder('Contoh: RPL, MIPA, IPS')
                    ->visible(function (callable $get) {
                        $schoolId = $get('school_id') ?? auth()->user()?->school_id;
                        if (!$schoolId) return true; // Tampilkan secara default jika belum memilih sekolah

                        $school = \App\Models\School::find($schoolId);
                        return $school && in_array($school->level, ['sma', 'smk', 'ma'], true);
                    })
                    ->helperText('Hanya muncul dan diisi untuk jenjang pendidikan menengah (SMA, SMK, MA).'),
                    
                Select::make('homeroom_teacher_id')
                    ->label('Wali Kelas')
                    ->relationship('homeroomTeacher', 'name')
                    ->searchable()
                    ->preload(),
                    
                TextInput::make('capacity')
                    ->label('Kapasitas Kelas (Siswa)')
                    ->required()
                    ->numeric()
                    ->default(36),
                    
                TextInput::make('room_number')
                    ->label('Nama / Nomor Ruang Kelas')
                    ->placeholder('Contoh: Ruang Belajar 101'),
                    
                Select::make('status')
                    ->label('Status')
                    ->options(['active' => 'Aktif', 'inactive' => 'Tidak Aktif'])
                    ->default('active')
                    ->native(false)
                    ->required(),
            ]);
    }
}
