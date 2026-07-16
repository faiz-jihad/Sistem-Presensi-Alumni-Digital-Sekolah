<?php

namespace App\Filament\Resources\Schedules\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Tables\Enums\RecordActionsPosition;

use Filament\Tables\Enums\FiltersLayout;

class SchedulesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('class.name')
                    ->label('Kelas')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('subject.name')
                    ->label('Mata Pelajaran')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('teacher.name')
                    ->label('Guru Pengampu')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('classHourPackage.name')
                    ->label('Paket Jam')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('classHour')
                    ->label('Jam Pelajaran')
                    ->formatStateUsing(function ($record) {
                        if (!$record->classHour) return '-';
                        return "Jam Ke-{$record->classHour->order} ({$record->classHour->code})";
                    })
                    ->sortable(),
                TextColumn::make('day')
                    ->label('Hari')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state?->value ?? $state) {
                        'monday' => 'Senin',
                        'tuesday' => 'Selasa',
                        'wednesday' => 'Rabu',
                        'thursday' => 'Kamis',
                        'friday' => 'Jumat',
                        'saturday' => 'Sabtu',
                        'sunday' => 'Minggu',
                        default => $state,
                    })
                    ->sortable(),
                TextColumn::make('room')
                    ->label('Ruangan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('school.name')
                    ->label('Sekolah')
                    ->searchable()
                    ->sortable()
                    ->visible(fn () => auth()->user()->role === 'super_admin'),
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('school_id')
                    ->label('Sekolah')
                    ->relationship('school', 'name')
                    ->placeholder('Semua Sekolah')
                    ->preload()
                    ->searchable()
                    ->visible(fn () => auth()->user()->isSuperAdmin()),

                SelectFilter::make('class_id')
                    ->label('Kelas')
                    ->relationship('class', 'name')
                    ->placeholder('Semua Kelas')
                    ->preload()
                    ->searchable(),

                SelectFilter::make('subject_id')
                    ->label('Mata Pelajaran')
                    ->relationship('subject', 'name')
                    ->placeholder('Semua Mata Pelajaran')
                    ->preload()
                    ->searchable(),

                SelectFilter::make('teacher_id')
                    ->label('Guru Pengampu')
                    ->relationship('teacher', 'name')
                    ->placeholder('Semua Guru')
                    ->preload()
                    ->searchable()
                    ->visible(fn () => auth()->user()->role !== 'teacher'),

                SelectFilter::make('day')
                    ->label('Hari')
                    ->options([
                        'monday' => 'Senin',
                        'tuesday' => 'Selasa',
                        'wednesday' => 'Rabu',
                        'thursday' => 'Kamis',
                        'friday' => 'Jumat',
                        'saturday' => 'Sabtu',
                        'sunday' => 'Minggu',
                    ])
                    ->placeholder('Semua Hari'),

                SelectFilter::make('is_active')
                    ->label('Status Aktif')
                    ->options([
                        '1' => 'Aktif',
                        '0' => 'Tidak Aktif',
                    ])
                    ->placeholder('Semua Status'),
            ], layout: FiltersLayout::Modal)
            ->filtersFormColumns(2)
            ->filtersTriggerAction(fn ($action) => $action
                ->button()
                ->label('Filter')
                ->icon('heroicon-m-funnel')
                ->color('gray')
            )
            ->headerActions([
                \Filament\Actions\Action::make('create_break_schedule')
                    ->label('Tambah Jam Istirahat')
                    ->icon('heroicon-o-clock')
                    ->color('warning')
                    ->button()
                    ->url(fn () => \App\Filament\Resources\Schedules\ScheduleResource::getUrl('create-break')),
                \Filament\Actions\Action::make('create_schedule')
                    ->label('Tambah Jadwal')
                    ->icon('heroicon-o-plus')
                    ->button()
                    ->url(fn () => \App\Filament\Resources\Schedules\ScheduleResource::getUrl('create')),
            ])
            ->recordActions([
                \Filament\Actions\EditAction::make()
                    ->url(fn ($record) => $record->classHour?->is_break
                        ? \App\Filament\Resources\Schedules\ScheduleResource::getUrl('edit-break', ['record' => $record->id])
                        : \App\Filament\Resources\Schedules\ScheduleResource::getUrl('edit', ['record' => $record->id])
                    ),
            ])
            ->recordActionsPosition(RecordActionsPosition::BeforeColumns)
            ->recordActionsColumnLabel('Aksi')
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
