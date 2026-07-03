<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SchoolResource\Pages;
use App\Models\School;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class SchoolResource extends Resource
{
    protected static ?string $model = School::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-building-office-2';
    }

    public static function getNavigationLabel(): string
    {
        return 'Sekolah';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Master Data';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public static function schema(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('name')
                    ->label('Nama Sekolah')
                    ->required()
                    ->maxLength(255),
                TextInput::make('npsn')
                    ->label('NPSN')
                    ->maxLength(50),
                Select::make('level')
                    ->label('Jenjang')
                    ->options([
                        'SD' => 'SD', 'SMP' => 'SMP', 'SMA' => 'SMA', 'SMK' => 'SMK',
                    ]),
                Select::make('status')
                    ->label('Status')
                    ->options(['active' => 'Aktif', 'inactive' => 'Non-aktif'])
                    ->default('active'),
                TextInput::make('principal_name')
                    ->label('Kepala Sekolah'),
                Textarea::make('address')
                    ->label('Alamat'),
                TextInput::make('phone')
                    ->label('Telepon')
                    ->tel(),
                TextInput::make('email')
                    ->label('Email')
                    ->email(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nama')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('npsn')->label('NPSN')->searchable(),
                Tables\Columns\TextColumn::make('level')->label('Jenjang')->badge(),
                Tables\Columns\TextColumn::make('status')->label('Status')->badge()
                    ->color(fn ($state) => $state === 'active' ? 'success' : 'danger'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('name');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSchools::route('/'),
            'create' => Pages\CreateSchool::route('/create'),
            'edit' => Pages\EditSchool::route('/{record}/edit'),
        ];
    }
}