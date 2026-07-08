<?php

namespace App\Filament\Resources\Subjects\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

class SubjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Mata Pelajaran')
                    ->components([
                        Select::make('school_id')
                            ->relationship('school', 'name')
                            ->label('Sekolah')
                            ->required()
                            ->visible(fn () => auth()->user()->role === 'super_admin')
                            ->default(fn () => auth()->user()->school_id),
                        TextInput::make('code')
                            ->label('Kode Mapel')
                            ->required()
                            ->maxLength(20)
                            ->placeholder('Contoh: MTK, IPA, BIN'),
                        TextInput::make('name')
                            ->label('Nama Mata Pelajaran')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: Matematika, Ilmu Pengetahuan Alam'),
                        TextInput::make('short_name')
                            ->label('Singkatan')
                            ->maxLength(10)
                            ->placeholder('Contoh: MTK'),
                        Select::make('group')
                            ->label('Kelompok')
                            ->options([
                                'general' => 'Umum (General)',
                                'specialized' => 'Kejuruan / Peminatan (Specialized)',
                                'local' => 'Muatan Lokal (Local)',
                                'extracurricular' => 'Ekstrakurikuler (Extracurricular)',
                            ])
                            ->default('general')
                            ->required()
                            ->native(false),
                        TextInput::make('credit_hours')
                            ->label('Beban Jam (JP)')
                            ->required()
                            ->numeric()
                            ->default(2),
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'active' => 'Aktif',
                                'inactive' => 'Tidak Aktif',
                            ])
                            ->default('active')
                            ->required()
                            ->native(false),
                        Textarea::make('description')
                            ->label('Deskripsi')
                            ->placeholder('Keterangan singkat mata pelajaran...')
                            ->columnSpanFull(),
                    ])
            ]);
    }
}
