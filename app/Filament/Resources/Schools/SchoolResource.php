<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SchoolResource\Pages;
use App\Models\School;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class SchoolResource extends Resource
{
    protected static ?string $model = School::class;
    public static function getNavigationIcon(): ?string { return 'heroicon-o-building-office-2'; }
    public static function getNavigationLabel(): string { return 'Sekolah'; }
    public static function getNavigationGroup(): ?string { return 'Master Data'; }
    public static function getNavigationSort(): ?int { return 1; }
    public static function schema(Schema $schema): Schema { return $schema; }
    public static function table(Table $table): Table { return $table; }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSchools::route('/'),
            'create' => Pages\CreateSchool::route('/create'),
            'edit' => Pages\EditSchool::route('/{record}/edit'),
        ];
    }
}
