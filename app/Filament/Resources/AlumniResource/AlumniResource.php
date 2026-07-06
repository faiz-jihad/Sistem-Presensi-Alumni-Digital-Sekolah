<?php

namespace App\Filament\Resources\AlumniResource;

use App\Filament\Resources\AlumniResource\Pages;
use App\Filament\Resources\AlumniResource\Schemas\AlumniForm;
use App\Filament\Resources\AlumniResource\Tables\AlumniTable;
use App\Models\Alumni;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class AlumniResource extends Resource
{
    protected static ?string $model = Alumni::class;

    protected static ?string $modelLabel = 'Alumni';

    protected static ?string $pluralModelLabel = 'Alumni';

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-users';
    }

    public static function getNavigationLabel(): string
    {
        return 'Alumni';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Data Alumni';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('verification_status', 'pending')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::where('verification_status', 'pending')->count() > 0 
            ? 'warning' 
            : 'success';
    }

    public static function form(Schema $schema): Schema
    {
        return AlumniForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AlumniTable::table($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAlumni::route('/'),
            'create' => Pages\CreateAlumni::route('/create'),
            'edit' => Pages\EditAlumni::route('/{record}/edit'),
        ];
    }
}