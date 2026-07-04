<?php

namespace App\Filament\Resources\Students\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class StudentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('school_id')
                    ->required()
                    ->numeric(),
                TextInput::make('class_id')
                    ->numeric(),
                TextInput::make('parent_user_id')
                    ->numeric(),
                TextInput::make('nis')
                    ->required(),
                TextInput::make('nisn')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                Select::make('gender')
                    ->options(['male' => 'Male', 'female' => 'Female'])
                    ->required(),
                DatePicker::make('birth_date')
                    ->required(),
                TextInput::make('birth_place'),
                Textarea::make('address')
                    ->columnSpanFull(),
                TextInput::make('photo'),
                TextInput::make('parent_name'),
                TextInput::make('parent_phone')
                    ->tel(),
                TextInput::make('enrollment_year')
                    ->numeric(),
                Select::make('status')
                    ->options([
            'active' => 'Active',
            'inactive' => 'Inactive',
            'graduated' => 'Graduated',
            'transferred' => 'Transferred',
            'dropout' => 'Dropout',
        ])
                    ->default('active')
                    ->required(),
            ]);
    }
}
