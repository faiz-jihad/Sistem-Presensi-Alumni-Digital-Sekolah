<?php

namespace App\Filament\Resources\Subjects\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class SubjectForm
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
                TextInput::make('code')
                    ->label('Kode Mata Pelajaran')
                    ->placeholder('Contoh: MTK, IPA, BIN')
                    ->required()
                    ->maxLength(20)
                    ->unique(ignorable: fn ($record) => $record, modifyRuleUsing: function (\Illuminate\Validation\Rules\Unique $rule, $get) {
                        return $rule->where('school_id', $get('school_id'));
                    }),
                TextInput::make('name')
                    ->label('Nama Mata Pelajaran')
                    ->placeholder('Contoh: Matematika, Ilmu Pengetahuan Alam')
                    ->required(),
                TextInput::make('short_name')
                    ->label('Singkatan')
                    ->placeholder('Contoh: MTK')
                    ->maxLength(10),
                Select::make('group')
                    ->label('Kelompok')
                    ->options([
                        'general' => 'Umum',
                        'specialized' => 'Peminatan / Kejuruan',
                        'local' => 'Muatan Lokal',
                        'extracurricular' => 'Ekstrakurikuler',
                    ])
                    ->default('general')
                    ->native(false)
                    ->required(),
                TextInput::make('credit_hours')
                    ->label('SKS / Jumlah Jam')
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
                    ->native(false)
                    ->required(),
                Textarea::make('description')
                    ->label('Keterangan')
                    ->placeholder('Keterangan tambahan mata pelajaran...')
                    ->columnSpanFull(),
            ]);
    }
}
