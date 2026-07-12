<?php

namespace App\Filament\Resources\StudentAttendances;

use App\Filament\Resources\StudentAttendances\Pages\CreateStudentAttendance;
use App\Filament\Resources\StudentAttendances\Pages\EditStudentAttendance;
use App\Filament\Resources\StudentAttendances\Pages\ListStudentAttendances;
use App\Filament\Resources\StudentAttendances\Pages\ManualAttendance;
use App\Filament\Resources\StudentAttendances\Schemas\StudentAttendanceForm;
use App\Filament\Resources\StudentAttendances\Tables\StudentAttendancesTable;
use App\Models\StudentAttendance;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class StudentAttendanceResource extends Resource
{
    protected static ?string $model = StudentAttendance::class;

    public static function canViewAny(): bool
    {
        return in_array(auth()->user()->role, ['super_admin', 'admin', 'teacher'])
            && auth()->user()->hasFeature('has_presensi');
    }

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationLabel = 'Presensi Harian';

    protected static ?string $modelLabel = 'Presensi Siswa';

    protected static ?string $pluralModelLabel = 'Presensi Harian';

    protected static string|\UnitEnum|null $navigationGroup = 'Presensi';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'date';

    public static function form(Schema $schema): Schema
    {
        return StudentAttendanceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StudentAttendancesTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->with(['student', 'class', 'teacher']);

        $user = auth()->user();

        // Admin sekolah hanya melihat data sekolahnya sendiri
        if ($user->role !== 'super_admin' && $user->school_id) {
            $query->where('school_id', $user->school_id);
        }

        return $query;
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListStudentAttendances::route('/'),
            'create' => CreateStudentAttendance::route('/create'),
            'edit'   => EditStudentAttendance::route('/{record}/edit'),
            'manual' => ManualAttendance::route('/manual'),
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
