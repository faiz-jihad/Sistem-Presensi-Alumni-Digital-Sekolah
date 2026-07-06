<?php

namespace App\Filament\Resources\Students;

use App\Filament\Resources\Students\Pages\CreateStudent;
use App\Filament\Resources\Students\Pages\EditStudent;
use App\Filament\Resources\Students\Pages\ListStudents;
use App\Filament\Resources\Students\Schemas\StudentForm;
use App\Filament\Resources\Students\Tables\StudentsTable;
use App\Models\Student;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $modelLabel = 'Siswa';

    protected static ?string $pluralModelLabel = 'Siswa';
    
    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-user-group';
    }

    public static function getNavigationLabel(): string
    {
        return 'Siswa';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Manajemen Akademik';
    }

    public static function getNavigationSort(): ?int
    {
        return 4;
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'nis', 'nisn', 'parent_name'];
    }

    public static function form(Schema $schema): Schema
    {
        return StudentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StudentsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStudents::route('/'),
            'create' => CreateStudent::route('/create'),
            'edit' => EditStudent::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}