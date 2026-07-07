<?php

namespace App\Filament\Resources\ClassHours;

use App\Filament\Resources\ClassHours\Pages\CreateClassHour;
use App\Filament\Resources\ClassHours\Pages\EditClassHour;
use App\Filament\Resources\ClassHours\Pages\ListClassHours;
use App\Filament\Resources\ClassHours\Schemas\ClassHourForm;
use App\Filament\Resources\ClassHours\Tables\ClassHoursTable;
use App\Models\ClassHour;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class ClassHourResource extends Resource
{
    protected static ?string $model = ClassHour::class;
    
    public static function canViewAny(): bool
    {
        return in_array(auth()->user()->role, ['super_admin', 'admin', 'teacher'])
            && auth()->user()->hasFeature('has_presensi');
    }
    
    protected static ?string $recordTitleAttribute = 'code';

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-clock';
    }

    public static function getNavigationLabel(): string
    {
        return 'Jam Pelajaran';
    }

    public static function getModelLabel(): string
    {
        return 'Jam Pelajaran';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Daftar Jam Pelajaran';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Data Master & Jadwal';
    }

    public static function getNavigationSort(): ?int
    {
        return 6;
    }

    public static function form(Schema $schema): Schema
    {
        return ClassHourForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ClassHoursTable::configure($table);
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
            'index' => ListClassHours::route('/'),
            'create' => CreateClassHour::route('/create'),
            'edit' => EditClassHour::route('/{record}/edit'),
        ];
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
