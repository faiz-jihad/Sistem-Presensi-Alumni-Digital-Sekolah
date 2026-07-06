<?php

namespace App\Filament\Resources\ClassHours\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ClassHourForm
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
                    ->label('Kode Jam Pelajaran')
                    ->placeholder('Contoh: J1, J2, IST1')
                    ->required()
                    ->maxLength(10)
                    ->unique(ignorable: fn ($record) => $record, modifyRuleUsing: function (\Illuminate\Validation\Rules\Unique $rule, $get) {
                        return $rule->where('school_id', $get('school_id'));
                    }),
                TimePicker::make('start_time')
                    ->label('Jam Mulai')
                    ->seconds(false)
                    ->required(),
                TimePicker::make('end_time')
                    ->label('Jam Selesai')
                    ->seconds(false)
                    ->required(),
                TextInput::make('duration_minutes')
                    ->label('Durasi (Menit)')
                    ->numeric()
                    ->required()
                    ->default(45),
                TextInput::make('order')
                    ->label('Urutan Jam Ke-')
                    ->numeric()
                    ->required()
                    ->default(1),
                Select::make('shift')
                    ->label('Shift')
                    ->options([
                        'morning' => 'Pagi',
                        'afternoon' => 'Siang',
                        'evening' => 'Malam',
                    ])
                    ->default('morning')
                    ->native(false)
                    ->required(),
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Aktif',
                        'inactive' => 'Tidak Aktif',
                    ])
                    ->default('active')
                    ->native(false)
                    ->required(),
                Toggle::make('is_break')
                    ->label('Jam Istirahat')
                    ->default(false),
            ]);
    }
}
