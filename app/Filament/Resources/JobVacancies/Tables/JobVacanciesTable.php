<?php

namespace App\Filament\Resources\JobVacancies\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use Filament\Tables\Enums\RecordActionsPosition;

class JobVacanciesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('school.name')
                    ->label('Sekolah')
                    ->searchable(),
                TextColumn::make('postedBy.name')
                    ->label('Dibuat Oleh')
                    ->searchable(),
                TextColumn::make('title')
                    ->label('Lowongan')
                    ->searchable(),
                TextColumn::make('company_name')
                    ->label('Nama Perusahaan')
                    ->searchable()
                    ->sortable(),
                ImageColumn::make('company_logo')
                    ->disk('public')
                    ->circular()
                    ->label('Logo'),
                TextColumn::make('location')
                    ->label('Lokasi')
                    ->searchable()
                    ->icon('heroicon-o-map-pin'),
                TextColumn::make('salary_min')
                    ->label('Gaji')
                    ->formatStateUsing(function ($record) {

                        if ($record->salary_min && $record->salary_max) {

                            return 'Rp '.number_format($record->salary_min,0,',','.')
                                .' - Rp '.number_format($record->salary_max,0,',','.');

                        }

                        return '-';

                    }),
                TextColumn::make('job_type')
                    ->label('Tipe Pekerjaan')
                    ->badge()
                    ->color(fn(string $state) => match ($state) {
                        'full_time' => 'success',
                        'part_time' => 'warning',
                        'freelance' => 'info',
                        'internship' => 'primary',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'full_time' => 'Penuh Waktu',
                        'part_time' => 'Paruh Waktu',
                        'freelance' => 'Pekerja Lepas',
                        'internship' => 'Magang',
                        default => $state,
                    }),
                TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'technology' => 'Teknologi',
                        'education' => 'Pendidikan',
                        'health' => 'Kesehatan',
                        'business' => 'Bisnis',
                        'creative' => 'Kreatif',
                        'engineering' => 'Teknik',
                        'others' => 'Lainnya',
                        default => $state,
                    }),
                TextColumn::make('deadline')
                    ->label('Tenggat Waktu')
                    ->date()
                    ->sortable()
                    ->color(fn ($state) => $state && $state->isPast() ? 'danger' : 'success'),
                IconColumn::make('is_active')
                    ->boolean()
                    ->label('Aktif'),
                TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Diperbarui Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('job_type')
                    ->label('Tipe Pekerjaan')
                    ->options([
                        'full_time' => 'Penuh Waktu',
                        'part_time' => 'Paruh Waktu',
                        'freelance' => 'Pekerja Lepas',
                        'internship' => 'Magang',
                    ]),

                SelectFilter::make('category')
                    ->label('Kategori')
                    ->options([
                        'technology'=>'Teknologi',
                        'education'=>'Pendidikan',
                        'health'=>'Kesehatan',
                        'business'=>'Bisnis',
                        'creative'=>'Kreatif',
                        'engineering'=>'Teknik',
                        'others'=>'Lainnya',
                    ]),
            ])
            ->recordActions([
                Action::make('toggle')
                ->label(fn($record)=>$record->is_active
                    ? 'Nonaktifkan'
                    : 'Aktifkan')

                ->icon('heroicon-o-check-circle')

                ->requiresConfirmation()

                ->action(function($record){

                    $record->update([

                        'is_active'=>!$record->is_active

                    ]);

                    Notification::make()

                        ->title('Status berhasil diubah')

                        ->success()

                        ->send();

                }),
                EditAction::make()
                    ->label('Edit'),
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
