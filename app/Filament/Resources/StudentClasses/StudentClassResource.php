<?php

namespace App\Filament\Resources\StudentClasses;

use App\Filament\Resources\StudentClasses\Pages\CreateStudentClass;
use App\Filament\Resources\StudentClasses\Pages\EditStudentClass;
use App\Filament\Resources\StudentClasses\Pages\ListStudentClasses;
use App\Filament\Resources\StudentClasses\Schemas\StudentClassForm;
use App\Filament\Resources\StudentClasses\Tables\StudentClassesTable;
use App\Models\StudentClass;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class StudentClassResource extends Resource
{
    protected static ?string $model = StudentClass::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

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
}
