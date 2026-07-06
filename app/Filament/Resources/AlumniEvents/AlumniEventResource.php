<?php

namespace App\Filament\Resources\AlumniEvents;

use App\Filament\Resources\AlumniEvents\Pages\CreateAlumniEvent;
use App\Filament\Resources\AlumniEvents\Pages\EditAlumniEvent;
use App\Filament\Resources\AlumniEvents\Pages\ListAlumniEvents;
use App\Filament\Resources\AlumniEvents\Pages\ViewAlumniEvent;
use App\Filament\Resources\AlumniEvents\Schemas\AlumniEventForm;
use App\Filament\Resources\AlumniEvents\Tables\AlumniEventsTable;
use App\Models\AlumniEvent;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AlumniEventResource extends Resource
{
    protected static ?string $model = AlumniEvent::class;

    protected static ?string $modelLabel = 'Event Alumni';

    protected static ?string $pluralModelLabel = 'Event Alumni';

    protected static ?string $recordTitleAttribute = 'title';

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-calendar';
    }

    public static function form(Schema $schema): Schema
    {
        return AlumniEventForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AlumniEventsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAlumniEvents::route('/'),
            'create' => CreateAlumniEvent::route('/create'),
            'view' => ViewAlumniEvent::route('/{record}'),
            'edit' => EditAlumniEvent::route('/{record}/edit'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return 'Event Alumni';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Data Alumni';
    }

    public static function getNavigationSort(): ?int
    {
        return 4;
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'title',
            'location',
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (auth()->user()->role !== 'super_admin') {
            $query->where('school_id', auth()->user()->school_id);
        }

        return $query;
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_active', true)->count();
    }
}
