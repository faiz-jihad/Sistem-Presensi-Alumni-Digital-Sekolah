<?php

namespace App\Filament\Resources\Schedules;

use App\Filament\Resources\Schedules\Pages\CreateSchedule;
use App\Filament\Resources\Schedules\Pages\EditSchedule;
use App\Filament\Resources\Schedules\Pages\ListSchedules;
use App\Filament\Resources\Schedules\Schemas\ScheduleForm;
use App\Filament\Resources\Schedules\Tables\SchedulesTable;
use App\Models\Schedule;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ScheduleResource extends Resource
{
    protected static ?string $model = Schedule::class;

    protected static ?string $modelLabel = 'Jadwal Pelajaran';

    protected static ?string $pluralModelLabel = 'Jadwal Pelajaran';

    protected static ?string $navigationLabel = 'Jadwal Pelajaran';

    protected static string|\UnitEnum|null $navigationGroup = 'Master Data';

    protected static ?int $navigationSort = 6;

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-calendar';
    }

    public static function canViewAny(): bool
    {
        return in_array(auth()->user()->role, ['super_admin', 'admin', 'teacher'])
            && auth()->user()->hasFeature('has_presensi');
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

    public static function form(Schema $schema): Schema
    {
        return ScheduleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SchedulesTable::configure($table);
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
            'index' => ListSchedules::route('/'),
            'create' => CreateSchedule::route('/create'),
            'edit' => EditSchedule::route('/{record}/edit'),
        ];
    }
}
