<?php

namespace App\Filament\Resources\ClassHourPackages\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ClassHoursRelationManager extends RelationManager
{
    protected static string $relationship = 'classHours';

    protected static ?string $title = 'Daftar Jam Pelajaran';

    protected static ?string $modelLabel = 'Jam Pelajaran';

    protected static ?string $pluralModelLabel = 'Jam Pelajaran';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('school_id')
                    ->relationship('school', 'name')
                    ->label('Sekolah')
                    ->required()
                    ->visible(fn () => auth()->user()->role === 'super_admin')
                    ->default(fn ($livewire) => $livewire->ownerRecord->school_id),
                TextInput::make('code')
                    ->label('Kode Jam')
                    ->required()
                    ->placeholder('Contoh: J1, J2, Istirahat'),
                TimePicker::make('start_time')
                    ->label('Jam Mulai')
                    ->seconds(false)
                    ->live()
                    ->afterStateUpdated(function ($state, $set, $get) {
                        self::updateDuration($set, $get);
                    }),
                TimePicker::make('end_time')
                    ->label('Jam Selesai')
                    ->seconds(false)
                    ->live()
                    ->afterStateUpdated(function ($state, $set, $get) {
                        self::updateDuration($set, $get);
                    }),
                TextInput::make('duration_minutes')
                    ->label('Durasi (Menit)')
                    ->numeric()
                    ->readOnly()
                    ->default(45)
                    ->required(),
                TextInput::make('order')
                    ->label('Urutan Jam Ke-')
                    ->required()
                    ->numeric()
                    ->placeholder('Contoh: 1, 2'),
                Toggle::make('is_break')
                    ->label('Jam Istirahat?')
                    ->default(false),
                Select::make('shift')
                    ->label('Shift')
                    ->options([
                        'morning' => 'Pagi (Morning)',
                        'afternoon' => 'Siang (Afternoon)',
                        'evening' => 'Sore (Evening)',
                    ])
                    ->default('morning')
                    ->required()
                    ->native(false),
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Aktif',
                        'inactive' => 'Tidak Aktif',
                    ])
                    ->default('active')
                    ->required()
                    ->native(false),
            ]);
    }

    public static function updateDuration($set, $get): void
    {
        $start = $get('start_time');
        $end   = $get('end_time');

        if (!$start || !$end) {
            return;
        }

        try {
            $startTime = \Carbon\Carbon::parse($start);
            $endTime   = \Carbon\Carbon::parse($end);

            $duration = $startTime->diffInMinutes($endTime, false);

            $set('duration_minutes', max($duration, 0));
        } catch (\Throwable $e) {
            //
        }
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('code')
            ->defaultSort('order', 'asc')
            ->columns([
                TextColumn::make('order')
                    ->label('Urutan')
                    ->sortable(),

                TextColumn::make('code')
                    ->label('Kode')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('start_time')
                    ->label('Jam Mulai')
                    ->time('H:i')
                    ->sortable(),

                TextColumn::make('end_time')
                    ->label('Jam Selesai')
                    ->time('H:i')
                    ->sortable(),

                TextColumn::make('duration_minutes')
                    ->label('Durasi')
                    ->suffix(' menit')
                    ->sortable(),

                IconColumn::make('is_break')
                    ->label('Istirahat')
                    ->boolean(),

                TextColumn::make('shift')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'morning' => 'Pagi',
                        'afternoon' => 'Siang',
                        'evening' => 'Sore',
                        default => $state,
                    }),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'active' => 'Aktif',
                        'inactive' => 'Tidak Aktif',
                        default => $state,
                    })
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->mutateFormDataUsing(function (array $data, $livewire): array {
                        $data['school_id'] = $livewire->ownerRecord->school_id;
                        return $data;
                    }),
            ])
            ->recordActions([
                EditAction::make()
                    ->mutateFormDataUsing(function (array $data, $livewire): array {
                        $data['school_id'] = $livewire->ownerRecord->school_id;
                        return $data;
                    }),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
