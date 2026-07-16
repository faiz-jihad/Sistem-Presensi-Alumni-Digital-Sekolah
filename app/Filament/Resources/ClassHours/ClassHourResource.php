<?php

namespace App\Filament\Resources\ClassHours;

use App\Filament\Resources\ClassHourPackages\ClassHourPackageResource;
use App\Filament\Resources\ClassHours\Pages\CreateClassHour;
use App\Filament\Resources\ClassHours\Pages\EditClassHour;
use App\Models\ClassHour;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;

class ClassHourResource extends Resource
{
    protected static ?string $model = ClassHour::class;

    protected static bool $shouldRegisterNavigation = false;

    public static function getIndexUrl(array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?\Illuminate\Database\Eloquent\Model $tenant = null, bool $shouldGuessMissingParameters = false): string
    {
        return ClassHourPackageResource::getUrl('index', $parameters, $isAbsolute, $panel, $tenant);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Grid::make(2)
                    ->components([
                        \Filament\Forms\Components\Hidden::make('class_hour_package_id')
                            ->default(fn () => request()->query('package_id')),
                        \Filament\Forms\Components\Select::make('school_id')
                            ->relationship('school', 'name')
                            ->label('Sekolah')
                            ->required()
                            ->visible(fn () => auth()->user()->role === 'super_admin')
                            ->default(fn () => request()->query('package_id') ? \App\Models\ClassHourPackage::find(request()->query('package_id'))?->school_id : auth()->user()->school_id),
                        \Filament\Forms\Components\TextInput::make('code')
                            ->label('Kode Jam')
                            ->required()
                            ->placeholder('Contoh: J1, J2, Istirahat'),
                        \Filament\Forms\Components\TimePicker::make('start_time')
                            ->label('Jam Mulai')
                            ->required()
                            ->seconds(false)
                            ->live()
                            ->afterStateUpdated(fn ($state, callable $set, callable $get) => self::updateDuration($set, $get)),
                        \Filament\Forms\Components\TimePicker::make('end_time')
                            ->label('Jam Selesai')
                            ->required()
                            ->seconds(false)
                            ->live()
                            ->afterStateUpdated(fn ($state, callable $set, callable $get) => self::updateDuration($set, $get)),
                        \Filament\Forms\Components\TextInput::make('duration_minutes')
                            ->label('Durasi (Menit)')
                            ->numeric()
                            ->readOnly()
                            ->default(45)
                            ->required(),
                        \Filament\Forms\Components\TextInput::make('order')
                            ->label('Urutan Jam Ke-')
                            ->required()
                            ->numeric()
                            ->placeholder('Contoh: 1, 2'),
                        \Filament\Forms\Components\Toggle::make('is_break')
                            ->label('Apakah Jam Ini Merupakan Jam Istirahat?')
                            ->default(false),
                        \Filament\Forms\Components\Select::make('shift')
                            ->label('Shift')
                            ->options([
                                'morning' => 'Pagi (Morning)',
                                'afternoon' => 'Siang (Afternoon)',
                                'evening' => 'Sore (Evening)',
                            ])
                            ->default('morning')
                            ->required()
                            ->native(false),
                        \Filament\Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'active' => 'Aktif',
                                'inactive' => 'Tidak Aktif',
                            ])
                            ->default('active')
                            ->required()
                            ->native(false),
                    ])
            ]);
    }

    public static function updateDuration(callable $set, callable $get): void
    {
        $start = $get('start_time');
        $end = $get('end_time');

        if (!$start || !$end) {
            return;
        }

        try {
            $startTime = \Carbon\Carbon::parse($start);
            $endTime = \Carbon\Carbon::parse($end);

            $duration = $startTime->diffInMinutes($endTime, false);

            $set('duration_minutes', max($duration, 0));
        } catch (\Throwable $e) {
            // Ignore parsing errors
        }
    }

    public static function getModelLabel(): string
    {
        return 'Jam Pelajaran';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Jam Pelajaran';
    }

    public static function getNavigationLabel(): string
    {
        return 'Jam Pelajaran';
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClassHours::route('/'),
            'create' => Pages\CreateClassHour::route('/create'),
            'edit' => Pages\EditClassHour::route('/{record}/edit'),
        ];
    }
}
