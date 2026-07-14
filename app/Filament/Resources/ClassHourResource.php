<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClassHourPackages\ClassHourPackageResource;
use App\Filament\Resources\ClassHourResource\Pages\CreateClassHour;
use App\Filament\Resources\ClassHourResource\Pages\EditClassHour;
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
                        \Filament\Schemas\Components\Group::make([
                            \Filament\Forms\Components\Select::make('school_id')
                                ->relationship('school', 'name')
                                ->label('Sekolah')
                                ->required()
                                ->visible(fn () => auth()->user()->role === 'super_admin')
                                ->default(fn () => auth()->user()->school_id),
                            \Filament\Forms\Components\TimePicker::make('start_time')
                                ->label('Jam Mulai')
                                ->required()
                                ->reactive()
                                ->afterStateUpdated(fn ($state, callable $set, callable $get) => self::updateDuration($set, $get)),
                            \Filament\Forms\Components\TimePicker::make('end_time')
                                ->label('Jam Selesai')
                                ->required()
                                ->reactive()
                                ->afterStateUpdated(fn ($state, callable $set, callable $get) => self::updateDuration($set, $get)),
                            \Filament\Forms\Components\Select::make('status')
                                ->label('Status')
                                ->options([
                                    'active' => 'Aktif',
                                    'inactive' => 'Tidak Aktif',
                                ])
                                ->default('active')
                                ->required()
                                ->native(false),
                        ]),
                        \Filament\Schemas\Components\Group::make([
                            \Filament\Forms\Components\TextInput::make('code')
                                ->label('Kode Jam')
                                ->required()
                                ->placeholder('Contoh: J1, J2, Istirahat'),
                            \Filament\Forms\Components\TextInput::make('order')
                                ->label('Urutan Jam Ke-')
                                ->required()
                                ->numeric()
                                ->placeholder('Contoh: 1, 2'),
                            \Filament\Forms\Components\TextInput::make('duration_minutes')
                                ->label('Durasi (Menit)')
                                ->required()
                                ->numeric()
                                ->default(45),
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
                            \Filament\Forms\Components\Toggle::make('is_break')
                                ->label('Jam Istirahat?')
                                ->default(false),
                        ]),
                    ])
            ]);
    }

    public static function updateDuration(callable $set, callable $get)
    {
        $start = $get('start_time');
        $end = $get('end_time');
        if ($start && $end) {
            try {
                $startTime = \Carbon\Carbon::parse($start);
                $endTime = \Carbon\Carbon::parse($end);
                if ($endTime->greaterThan($startTime)) {
                    $set('duration_minutes', $startTime->diffInMinutes($endTime));
                } else {
                    $set('duration_minutes', 0);
                }
            } catch (\Exception $e) {
                // Ignore parsing errors
            }
        }
    }

    public static function getPages(): array
    {
        return [
            'create' => CreateClassHour::route('/create'),
            'edit' => EditClassHour::route('/{record}/edit'),
        ];
    }
}
