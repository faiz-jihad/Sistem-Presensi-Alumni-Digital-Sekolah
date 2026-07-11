<?php

namespace App\Filament\Resources\ClassHourPackages;

use App\Filament\Resources\ClassHourPackages\Pages\CreateClassHourPackage;
use App\Filament\Resources\ClassHourPackages\Pages\EditClassHourPackage;
use App\Filament\Resources\ClassHourPackages\Pages\ListClassHourPackages;
use App\Filament\Resources\ClassHourPackages\Schemas\ClassHourPackageForm;
use App\Filament\Resources\ClassHourPackages\Tables\ClassHourPackagesTable;
use App\Models\ClassHourPackage;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

use App\Filament\Resources\ClassHourPackages\RelationManagers\ClassHoursRelationManager;

class ClassHourPackageResource extends Resource
{
    protected static ?string $model = ClassHourPackage::class;

    protected static ?string $modelLabel = 'Paket Jam Pelajaran';

    protected static ?string $pluralModelLabel = 'Paket Jam Pelajaran';

    protected static ?string $navigationLabel = 'Paket Jam Pelajaran';

    protected static string|\UnitEnum|null $navigationGroup = 'Akademik';

    protected static ?int $navigationSort = 4;

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-clock';
    }

    public static function canViewAny(): bool
    {
        return in_array(auth()->user()->role, [
            'super_admin',
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

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();

        if (auth()->user()->role !== 'super_admin') {
            $query->where('school_id', auth()->user()->school_id);
        }

        return $query;
    }

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return ClassHourPackageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ClassHourPackagesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ClassHoursRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListClassHourPackages::route('/'),
            'create' => CreateClassHourPackage::route('/create'),
            'edit' => EditClassHourPackage::route('/{record}/edit'),
        ];
    }
}
