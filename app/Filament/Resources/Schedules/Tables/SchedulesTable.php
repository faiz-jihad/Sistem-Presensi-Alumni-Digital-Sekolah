<?php

namespace App\Filament\Resources\Schedules\Tables;

use App\Enums\DayOfWeek;
use App\Models\Schedule;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class SchedulesTable
{
    public static function configure(Table $table): Table
    {
        $caseExpression = "CASE day 
            WHEN 'monday' THEN 1 
            WHEN 'tuesday' THEN 2 
            WHEN 'wednesday' THEN 3 
            WHEN 'thursday' THEN 4 
            WHEN 'friday' THEN 5 
            WHEN 'saturday' THEN 6 
            WHEN 'sunday' THEN 7 
            ELSE 8 
        END";

        return $table
            ->contentGrid([
                'sm' => 1,
                'md' => 2,
                'lg' => 3,
                'xl' => 4,
            ])
            ->columns([
                Stack::make([
                    // Row 1: Kelas (badge) & Ruangan
                    Split::make([
                        TextColumn::make('class.name')
                            ->badge()
                            ->color('primary')
                            ->searchable(),
                        
                        TextColumn::make('room')
                            ->icon('heroicon-m-map-pin')
                            ->color('gray')
                            ->size('sm')
                            ->alignEnd()
                            ->placeholder('R. Belajar')
                            ->searchable(),
                    ])
                    ->extraAttributes(['class' => 'flex justify-between items-center w-full']),

                    // Row 2: Mata Pelajaran (Bold & Large)
                    TextColumn::make('subject.name')
                        ->weight('bold')
                        ->size('lg')
                        ->description(fn ($record) => "Kode: " . ($record->subject?->code ?? '-'))
                        ->searchable(),

                    // Row 3: Guru Pengampu
                    TextColumn::make('teacher.name')
                        ->icon('heroicon-m-user')
                        ->color('gray')
                        ->size('sm')
                        ->searchable(),

                    // Row 4: Jam Pelajaran & Hari
                    Split::make([
                        TextColumn::make('classHour.code')
                            ->icon('heroicon-m-clock')
                            ->formatStateUsing(fn ($record) => $record->classHour ? "Jam " . $record->classHour->code . " (" . substr($record->classHour->start_time, 0, 5) . ' - ' . substr($record->classHour->end_time, 0, 5) . ")" : '-')
                            ->color('info')
                            ->size('sm'),

                        TextColumn::make('day')
                            ->badge()
                            ->alignEnd()
                            ->color(fn ($state): string => match ($state instanceof DayOfWeek ? $state->value : $state) {
                                'monday', 'tuesday', 'wednesday', 'thursday', 'friday' => 'info',
                                'saturday', 'sunday' => 'warning',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn ($state): string => $state instanceof DayOfWeek ? $state->label() : match ($state) {
                                'monday' => 'Senin',
                                'tuesday' => 'Selasa',
                                'wednesday' => 'Rabu',
                                'thursday' => 'Kamis',
                                'friday' => 'Jumat',
                                'saturday' => 'Sabtu',
                                'sunday' => 'Minggu',
                                default => (string) $state,
                            }),
                    ])
                    ->extraAttributes(['class' => 'flex justify-between items-center w-full']),

                    // Row 5: Allow Early Open & Active Toggle (with Custom Labels)
                    Split::make([
                        Split::make([
                            TextColumn::make('allow_early_open_label')
                                ->state('Buka Awal')
                                ->color('gray')
                                ->size('xs')
                                ->extraAttributes(['class' => 'self-center mr-2']),
                            ToggleColumn::make('allow_early_open')
                                ->label(''),
                        ])
                        ->extraAttributes(['class' => 'flex items-center gap-1']),
                        
                        Split::make([
                            TextColumn::make('is_active_label')
                                ->state('Status Aktif')
                                ->color('gray')
                                ->size('xs')
                                ->extraAttributes(['class' => 'self-center mr-2']),
                            ToggleColumn::make('is_active')
                                ->label(''),
                        ])
                        ->extraAttributes(['class' => 'flex items-center gap-1 justify-end']),
                    ])
                    ->extraAttributes(['class' => 'border-t border-gray-100 dark:border-gray-800 pt-3 mt-3 flex justify-between items-center w-full']),
                ])
                ->space(3)
                ->extraAttributes(['class' => 'p-4 bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-100 dark:border-gray-800']),
            ])
            ->groups([
                Group::make('day')
                    ->label('Hari')
                    ->collapsible()
                    ->getTitleFromRecordUsing(fn (Schedule $record): string => $record->day instanceof DayOfWeek ? $record->day->label() : $record->day)
                    ->orderQueryUsing(fn ($query, $direction) => $query->orderByRaw("$caseExpression $direction")),
            ])
            ->defaultGroup('day')
            ->defaultSort('day', 'asc')
            ->filters([
                SelectFilter::make('class_id')
                    ->label('Filter Kelas')
                    ->relationship('class', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('subject_id')
                    ->label('Filter Mata Pelajaran')
                    ->relationship('subject', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('teacher_id')
                    ->label('Filter Guru')
                    ->relationship('teacher', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('day')
                    ->label('Filter Hari')
                    ->options(DayOfWeek::options()),

                TernaryFilter::make('is_active')
                    ->label('Status Aktif')
                    ->boolean()
                    ->trueLabel('Aktif Saja')
                    ->falseLabel('Nonaktif Saja')
                    ->native(false),
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
