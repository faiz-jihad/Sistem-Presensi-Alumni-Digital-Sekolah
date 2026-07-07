<?php

namespace App\Filament\Resources\PresensiSessions;

use App\Filament\Resources\PresensiSessions\Pages\CreatePresensiSession;
use App\Filament\Resources\PresensiSessions\Pages\EditPresensiSession;
use App\Filament\Resources\PresensiSessions\Pages\ListPresensiSessions;
use App\Filament\Resources\PresensiSessions\Pages\QrPresensiPage;
use App\Filament\Resources\PresensiSessions\Schemas\PresensiSessionForm;
use App\Filament\Resources\PresensiSessions\Tables\PresensiSessionsTable;
use App\Models\PresensiSession;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PresensiSessionResource extends Resource
{
    protected static ?string $model = PresensiSession::class;

    public static function canViewAny(): bool
    {
        return in_array(auth()->user()->role, ['super_admin', 'admin', 'teacher'])
            && auth()->user()->hasFeature('has_presensi');
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQueueList;

    protected static ?string $navigationLabel = 'Sesi Presensi Kelas';

    protected static ?string $modelLabel = 'Sesi Presensi';

    protected static ?string $pluralModelLabel = 'Sesi Presensi Kelas';

    protected static string|\UnitEnum|null $navigationGroup = 'Presensi & Kehadiran';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'date';

    public static function form(Schema $schema): Schema
    {
        return PresensiSessionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PresensiSessionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListPresensiSessions::route('/'),
            'create' => CreatePresensiSession::route('/create'),
            'edit'   => EditPresensiSession::route('/{record}/edit'),
            'qr'     => QrPresensiPage::route('/{record}/qr'),
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
