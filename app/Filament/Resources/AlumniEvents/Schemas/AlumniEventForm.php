<?php

namespace App\Filament\Resources\AlumniEvents\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;

class AlumniEventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('school_id')
                    ->label('Sekolah')
                    ->relationship('school', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->default(fn () => auth()->user()->school_id)
                    ->disabled(fn () => auth()->user()->role !== 'super_admin')
                    ->dehydrated(),
                Hidden::make('posted_by')
                    ->default(fn () => auth()->id()),
                TextInput::make('title')
                    ->label('Judul Kegiatan')
                    ->required()
                    ->maxLength(255),
                DateTimePicker::make('event_date')
                    ->label('Waktu Pelaksanaan')
                    ->required(),
                TextInput::make('location')
                    ->label('Lokasi')
                    ->required()
                    ->placeholder('Contoh: Aula Sekolah atau Link Zoom')
                    ->maxLength(255),
                FileUpload::make('banner_image')
                    ->label('Banner / Poster Kegiatan')
                    ->directory('event-banners')
                    ->disk('public')
                    ->image(),
                RichEditor::make('description')
                    ->label('Deskripsi Kegiatan')
                    ->columnSpanFull()
                    ->required(),
                Toggle::make('is_active')
                    ->label('Aktif')
                    ->required()
                    ->default(true),
            ]);
    }
}
