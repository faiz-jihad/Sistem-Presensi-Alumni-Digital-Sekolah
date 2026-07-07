<?php

namespace App\Filament\Resources\AcademicYears;

use App\Filament\Resources\AcademicYears\Pages\CreateAcademicYear;
use App\Filament\Resources\AcademicYears\Pages\EditAcademicYear;
use App\Filament\Resources\AcademicYears\Pages\ListAcademicYears;
use App\Filament\Resources\AcademicYears\Schemas\AcademicYearForm;
use App\Filament\Resources\AcademicYears\Tables\AcademicYearsTable;
use App\Models\AcademicYear;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AcademicYearResource extends Resource
{
    protected static ?string $model = AcademicYear::class;

    public static function canViewAny(): bool
    {
        return in_array(auth()->user()->role, ['super_admin', 'admin', 'teacher']);
    }

    protected static ?string $modelLabel = 'Tahun Akademik';

    protected static ?string $pluralModelLabel = 'Tahun Akademik';
    
    protected static ?string $recordTitleAttribute = 'name';

    // Gunakan method
    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-rectangle-stack';
    }

    public static function getNavigationLabel(): string
    {
        return 'Tahun Ajaran';
    }

    public static function getModelLabel(): string
    {
        return 'Tahun Ajaran';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Daftar Tahun Ajaran';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Master Data';
    }

    public static function getNavigationSort(): ?int
    {
        return 2;
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name'];
    }

    public static function form(Schema $schema): Schema
    {
        return AcademicYearForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AcademicYearsTable::configure($table);
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
            'index' => ListAcademicYears::route('/'),
            'create' => CreateAcademicYear::route('/create'),
            'edit' => EditAcademicYear::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();

        if ($user->role === 'super_admin') {
            return true;
        }

        return $user->school?->status === 'active';
    }
}