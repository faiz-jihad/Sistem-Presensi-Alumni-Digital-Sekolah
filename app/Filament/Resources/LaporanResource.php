<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LaporanResource\Pages;
use App\Filament\Resources\LaporanResource\Schemas\LaporanForm;
use App\Filament\Resources\LaporanResource\Tables\LaporanTable;
use App\Models\Export;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class LaporanResource extends Resource
{
    protected static ?string $model = Export::class;

    public static function canViewAny(): bool
    {
        return in_array(auth()->user()->role, ['super_admin', 'admin', 'teacher'])
            && auth()->user()->hasFeature('has_export');
    }

    protected static ?string $modelLabel = 'Laporan';

    protected static ?string $pluralModelLabel = 'Laporan';

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-document-arrow-down';
    }

    public static function getNavigationLabel(): string
    {
        return 'Laporan';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Data Alumni';
    }

    public static function getNavigationSort(): ?int
    {
        return 2;
    }

    public static function form(Schema $schema): Schema
    {
        return LaporanForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LaporanTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLaporans::route('/'),
            'create' => Pages\CreateLaporan::route('/create'),
            'view' => Pages\ViewLaporan::route('/{record}'),
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
