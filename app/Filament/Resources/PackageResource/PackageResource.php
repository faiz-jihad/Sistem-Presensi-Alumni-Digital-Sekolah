<?php

namespace App\Filament\Resources\PackageResource;

use App\Models\Package;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use App\Filament\Resources\PackageResource\Pages\ListPackages;
use App\Filament\Resources\PackageResource\Pages\CreatePackage;
use App\Filament\Resources\PackageResource\Pages\EditPackage;
class PackageResource extends Resource
{
    protected static ?string $model = Package::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function canViewAny(): bool
    {
        return auth()->user()->role === 'super_admin';
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-gift';
    }

    public static function getNavigationLabel(): string
    {
        return 'Paket Langganan';
    }

    public static function getModelLabel(): string
    {
        return 'Paket';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Daftar Paket';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Master Data';
    }

    public static function getNavigationSort(): ?int
    {
        return 2;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Informasi Dasar Paket')
                ->description('Tentukan informasi komersial dan dasar untuk paket langganan ini.')
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('name')
                            ->label('Nama Paket')
                            ->required()
                            ->maxLength(100)
                            ->placeholder('Contoh: Basic, Standard, Premium')
                            ->unique(ignoreRecord: true),

                        TextInput::make('price')
                            ->label('Harga per Bulan (Rp)')
                            ->numeric()
                            ->default(0)
                            ->prefix('Rp')
                            ->minValue(0)
                            ->helperText('Isi 0 jika paket gratis/free-tier'),

                        TextInput::make('duration_months')
                            ->label('Durasi (Bulan)')
                            ->numeric()
                            ->default(1)
                            ->minValue(1)
                            ->suffix('bulan')
                            ->required(),

                        Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->default(true)
                            ->helperText('Paket tidak aktif tidak akan muncul sebagai pilihan bagi sekolah baru'),
                    ]),

                    Textarea::make('description')
                        ->label('Deskripsi Paket')
                        ->rows(3)
                        ->maxLength(500)
                        ->placeholder('Deskripsi singkat mengenai paket ini...')
                        ->columnSpanFull(),
                ])
                ->columnSpanFull(),

            Section::make('Akses Fitur Paket')
                ->description('Centang fitur-fitur yang diperbolehkan diakses oleh sekolah yang membeli paket ini.')
                ->schema([
                    Grid::make(5)->schema([
                        Toggle::make('has_presensi')
                            ->label('Fitur Presensi')
                            ->default(true)
                            ->inline(false),

                        Toggle::make('has_alumni')
                            ->label('Data Alumni')
                            ->default(true)
                            ->inline(false),

                        Toggle::make('has_tracer_study')
                            ->label('Tracer Study')
                            ->default(true)
                            ->inline(false),

                        Toggle::make('has_job_vacancy')
                            ->label('Lowongan Kerja')
                            ->default(true)
                            ->inline(false),

                        Toggle::make('has_export')
                            ->label('Ekspor Laporan')
                            ->default(true)
                            ->inline(false),
                    ]),
                ])
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Paket')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('price')
                    ->label('Harga / Bulan')
                    ->formatStateUsing(fn ($state) => $state == 0 ? 'Gratis' : 'Rp ' . number_format($state, 0, ',', '.'))
                    ->badge()
                    ->color(fn ($state) => $state == 0 ? 'success' : 'primary')
                    ->sortable(),

                TextColumn::make('duration_months')
                    ->label('Durasi')
                    ->formatStateUsing(fn ($state) => $state . ' bulan')
                    ->sortable(),

                // Status indicators for features
                IconColumn::make('has_presensi')
                    ->label('Presensi')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('danger'),

                IconColumn::make('has_alumni')
                    ->label('Alumni')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('danger'),

                IconColumn::make('has_tracer_study')
                    ->label('Tracer')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('danger'),

                IconColumn::make('has_job_vacancy')
                    ->label('Loker')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('danger'),

                IconColumn::make('has_export')
                    ->label('Ekspor')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('danger'),

                TextColumn::make('schools_count')
                    ->label('Pengguna')
                    ->counts('schools')
                    ->badge()
                    ->color('info'),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Status Aktif')
                    ->trueLabel('Aktif')
                    ->falseLabel('Tidak Aktif')
                    ->placeholder('Semua'),
            ])
            ->actions([
                EditAction::make()
                    ->label('Edit')
                    ->icon('heroicon-m-pencil-square')
                    ->color('warning'),

                DeleteAction::make()
                    ->label('Hapus')
                    ->icon('heroicon-m-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Hapus Paket?')
                    ->modalDescription('Sekolah yang menggunakan paket ini akan dilepas dari paket. Lanjutkan?')
                    ->before(function (Package $record) {
                        $record->schools()->update(['package_id' => null]);
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Hapus Terpilih')
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Paket Terpilih?'),
                ]),
            ])
            ->defaultSort('name', 'asc')
            ->emptyStateHeading('Belum ada paket')
            ->emptyStateDescription('Buat paket pertama untuk mulai membedakan hak akses sekolah.')
            ->emptyStateIcon('heroicon-o-gift');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPackages::route('/'),
            'create' => CreatePackage::route('/create'),
            'edit' => EditPackage::route('/{record}/edit'),
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
