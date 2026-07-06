<?php

namespace App\Filament\Resources\StudentClasses;

use App\Filament\Resources\StudentClasses\Pages\CreateStudentClass;
use App\Filament\Resources\StudentClasses\Pages\EditStudentClass;
use App\Filament\Resources\StudentClasses\Pages\ListStudentClasses;
use App\Filament\Resources\StudentClasses\Schemas\StudentClassForm;
use App\Filament\Resources\StudentClasses\Tables\StudentClassesTable;
use App\Models\StudentClass;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StudentClassResource extends Resource
{
    protected static ?string $model = StudentClass::class;

    public static function canViewAny(): bool
    {
        return in_array(auth()->user()->role, ['super_admin', 'admin', 'teacher']);
    }

    protected static ?string $modelLabel = 'Kelas Siswa';

    protected static ?string $pluralModelLabel = 'Kelas Siswa';
    
    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-academic-cap';
    }

    public static function getNavigationLabel(): string
    {
        return 'Kelas Siswa';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Manajemen Akademik';
    }

    public static function getNavigationSort(): ?int
    {
        return 3;
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'grade', 'major'];
    }

    public static function form(Schema $schema): Schema
    {
        return StudentClassForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StudentClassesTable::configure($table);
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
            'index' => ListStudentClasses::route('/'),
            'create' => CreateStudentClass::route('/create'),
            'edit' => EditStudentClass::route('/{record}/edit'),
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