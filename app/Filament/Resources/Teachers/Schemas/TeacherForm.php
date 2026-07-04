<?php

namespace App\Filament\Resources\Teachers\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class TeacherForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('school_id')
                    ->relationship('school', 'name')
                    ->required(),
                Select::make('user_id')
                    ->relationship('user', 'name'),
                TextInput::make('nip')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                Select::make('gender')
                    ->options(['male' => 'Male', 'female' => 'Female']),
                TextInput::make('phone')
                    ->tel(),
                Textarea::make('address')
                    ->columnSpanFull(),
                TextInput::make('photo'),
                Select::make('employment_status')
                    ->options([
            'pns' => 'Pns',
            'pppk' => 'Pppk',
            'honorer' => 'Honorer',
            'gtt' => 'Gtt',
            'ptt' => 'Ptt',
            'kontrak' => 'Kontrak',
        ])
                    ->default('honorer')
                    ->required(),
                TextInput::make('field_of_study'),
                TextInput::make('education_level'),
                TextInput::make('university'),
                DatePicker::make('join_date'),
                Select::make('status')
                    ->options(['active' => 'Active', 'inactive' => 'Inactive', 'retired' => 'Retired'])
                    ->default('active')
                    ->required(),
            ]);
    }
}
