<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Lengkap')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('role')
                    ->label('Peran')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'super_admin' => 'danger',
                        'admin' => 'warning',
                        'teacher' => 'success',
                        'student' => 'info',
                        'parent' => 'gray',
                        'alumni' => 'primary',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'super_admin' => 'Super Admin',
                        'admin' => 'Admin',
                        'teacher' => 'Guru',
                        'student' => 'Siswa',
                        'parent' => 'Orang Tua / Wali',
                        'alumni' => 'Alumni',
                        default => $state,
                    })
                    ->sortable(),
                TextColumn::make('school.name')
                    ->label('Sekolah')
                    ->searchable()
                    ->sortable()
                    ->visible(fn () => auth()->user()->isSuperAdmin()),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        'suspended' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Aktif',
                        'inactive' => 'Tidak Aktif',
                        'suspended' => 'Ditangguhkan',
                        default => $state,
                    })
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('school_id')
                    ->label('Sekolah')
                    ->relationship('school', 'name')
                    ->placeholder('Semua Sekolah')
                    ->preload()
                    ->searchable()
                    ->visible(fn () => auth()->user()->isSuperAdmin()),

                SelectFilter::make('role')
                    ->label('Peran')
                    ->options([
                        'super_admin' => 'Super Admin',
                        'admin' => 'Admin',
                        'teacher' => 'Guru',
                        'student' => 'Siswa',
                        'parent' => 'Orang Tua / Wali',
                        'alumni' => 'Alumni',
                    ]),

                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Aktif',
                        'inactive' => 'Tidak Aktif',
                        'suspended' => 'Ditangguhkan',
                    ]),

                SelectFilter::make('google_id')
                    ->label('Tautan Google')
                    ->options([
                        'connected' => 'Terhubung',
                        'disconnected' => 'Belum Terhubung',
                    ])
                    ->query(function (\Illuminate\Database\Eloquent\Builder $query, array $data) {
                        if ($data['value'] === 'connected') {
                            $query->whereNotNull('google_id');
                        } elseif ($data['value'] === 'disconnected') {
                            $query->whereNull('google_id');
                        }
                    }),

                TrashedFilter::make()
                    ->label('Sampah (Soft Delete)'),
            ], layout: FiltersLayout::Modal)
            ->filtersFormColumns(2)
            ->filtersTriggerAction(fn ($action) => $action
                ->button()
                ->label('Filter')
                ->icon('heroicon-m-funnel')
                ->color('gray')
            )
            ->actions([
                EditAction::make()
                    ->label('Edit'),

                DeleteAction::make(),
            ])
            ->actionsColumnLabel('Aksi')
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('name');
    }
}

