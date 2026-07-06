<?php

namespace App\Filament\Resources\TeacherSubjects;

use App\Filament\Resources\TeacherSubjects\Pages\CreateTeacherSubject;
use App\Filament\Resources\TeacherSubjects\Pages\EditTeacherSubject;
use App\Filament\Resources\TeacherSubjects\Pages\ListTeacherSubjects;
use App\Models\Teacher;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TeacherSubjectResource extends Resource
{
    protected static ?string $model = Teacher::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;

    protected static ?string $navigationLabel = 'Pembagian Tugas Guru';

    protected static ?string $modelLabel = 'Guru Mata Pelajaran';

    protected static ?string $pluralModelLabel = 'Pembagian Tugas Guru';

    protected static string|\UnitEnum|null $navigationGroup = 'Presensi & Kehadiran';

    protected static ?int $navigationSort = 3;

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('subjects')
                ->label('Mata Pelajaran yang Diampu')
                ->relationship('subjects', 'name')
                ->multiple()
                ->searchable()
                ->preload()
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Guru')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('nip')
                    ->label('NIP')
                    ->searchable(),
                TextColumn::make('subjects.name')
                    ->label('Mata Pelajaran')
                    ->badge()
                    ->separator(',')
                    ->searchable(),
                TextColumn::make('school.name')
                    ->label('Sekolah')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge(),
            ])
            ->filters([
                SelectFilter::make('school_id')
                    ->label('Sekolah')
                    ->relationship('school', 'name'),
            ])
            ->actions([
                EditAction::make()->label('Kelola Mapel'),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListTeacherSubjects::route('/'),
            'create' => CreateTeacherSubject::route('/create'),
            'edit'   => EditTeacherSubject::route('/{record}/edit'),
        ];
    }
}
