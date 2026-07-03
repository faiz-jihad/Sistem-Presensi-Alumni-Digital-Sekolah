<?php

namespace App\Filament\Resources\Schools\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class SchoolForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('npsn')
                    ->required()
                    ->maxLength(8)
                    ->unique(ignoreRecord: true),
                Textarea::make('address')
                    ->columnSpanFull(),
                TextInput::make('phone')
                    ->tel()
                    ->maxLength(20),
                TextInput::make('email')
                    ->label('Email address')
                    ->email(),
                TextInput::make('website')
                    ->url(),
                FileUpload::make('logo')
                    ->image()
                    ->directory('schools/logos'),
                TextInput::make('principal_name'),
                Select::make('level')
                    ->options(['sd' => 'Sd', 'smp' => 'Smp', 'sma' => 'Sma', 'smk' => 'Smk', 'ma' => 'Ma'])
                    ->default('smk')
                    ->required(),
                Select::make('accreditation')
                    ->options(['a' => 'A', 'b' => 'B', 'c' => 'C', 'not_accredited' => 'Not accredited']),
                Select::make('status')
                    ->options(['active' => 'Active', 'inactive' => 'Inactive'])
                    ->default('active')
                    ->required(),
            ]);
    }
}
