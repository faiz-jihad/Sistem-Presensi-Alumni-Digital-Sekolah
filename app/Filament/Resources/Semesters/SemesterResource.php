<?php

namespace App\Filament\Resources\Semesters;

use App\Filament\Resources\Semesters\Pages\CreateSemesters;
use App\Filament\Resources\Semesters\Pages\EditSemesters;
use App\Filament\Resources\Semesters\Pages\ListSemesters;
use App\Filament\Resources\Semesters\Schemas\SemesterForm;
use App\Filament\Resources\Semesters\Tables\SemestersTable;
use App\Models\Semester;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class SemesterResource extends Resource
{
    protected static ?string $model = Semester::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-calendar-days';
    }

    public static function getNavigationLabel(): string
    {
        return 'Semester';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Akademik';
    }

    public static function getNavigationSort(): ?int
    {
        return 3;
    }

    public static function canViewAny(): bool
    {
        return in_array(auth()->user()->role, [
            'super_admin',
            'admin',
        ]);
    }

    public static function form(Schema $schema): Schema
    {
        return SemesterForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SemestersTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSemesters::route('/'),
            'create' => CreateSemesters::route('/create'),
            'edit' => EditSemesters::route('/{record}/edit'),
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