<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentResource\Pages;
use App\Models\Student;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;
    public static function getNavigationIcon(): ?string { return 'heroicon-o-users'; }
    public static function getNavigationLabel(): string { return 'Siswa'; }
    public static function getNavigationGroup(): ?string { return 'Master Data'; }
    public static function getNavigationSort(): ?int { return 3; }
    public static function schema(Schema $schema): Schema { return $schema; }
    public static function table(Table $table): Table { return $table; }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
        ];
    }
}
