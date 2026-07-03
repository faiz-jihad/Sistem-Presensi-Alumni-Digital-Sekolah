<?php

namespace App\Filament\Resources\Students\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
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
                Select::make('school_id')
                    ->relationship('school', 'name')
                    ->required(),
                Select::make('class_id')
                    ->relationship('class', 'name'),
                Select::make('parent_user_id')
                    ->relationship('parent', 'name'),
                TextInput::make('nis')
                    ->required()
                    ->maxLength(20)
                    ->unique(ignoreRecord: true),
                TextInput::make('nisn')
                    ->required()
                    ->maxLength(10)
                    ->unique(ignoreRecord: true),
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
                FileUpload::make('photo')
                    ->image()
                    ->directory('students/photos'),
                TextInput::make('parent_name'),
                TextInput::make('parent_phone')
                    ->tel()
                    ->maxLength(20),
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
