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
                    ->relationship('school', 'name')
                    ->required(),
                Select::make('academic_year_id')
                    ->relationship('academicYear', 'name'),
                TextInput::make('name')
                    ->required(),
                Select::make('grade')
                    ->options(['10' => '10', '11' => '11', '12' => '12', '13' => '13'])
                    ->required(),
                TextInput::make('major'),
                Select::make('homeroom_teacher_id')
                    ->relationship('homeroomTeacher', 'name'),
                TextInput::make('capacity')
                    ->required()
                    ->numeric()
                    ->default(30),
                TextInput::make('room_number'),
                Select::make('status')
                    ->options(['active' => 'Active', 'inactive' => 'Inactive'])
                    ->default('active')
                    ->required(),
            ]);
    }
}
