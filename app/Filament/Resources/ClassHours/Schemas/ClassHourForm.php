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
                    ->relationship('school', 'name')
                    ->required(),
                TextInput::make('code')
                    ->required(),
                TimePicker::make('start_time')
                    ->required(),
                TimePicker::make('end_time')
                    ->required(),
                TextInput::make('duration_minutes')
                    ->required()
                    ->numeric(),
                TextInput::make('order')
                    ->required()
                    ->numeric(),
                Toggle::make('is_break')
                    ->required(),
                Select::make('shift')
                    ->options(['morning' => 'Morning', 'afternoon' => 'Afternoon', 'evening' => 'Evening'])
                    ->default('morning')
                    ->required(),
                Select::make('status')
                    ->options(['active' => 'Active', 'inactive' => 'Inactive'])
                    ->default('active')
                    ->required(),
                Select::make('class_hour_package_id')
                    ->relationship('classHourPackage', 'name'),
            ]);
    }
}
