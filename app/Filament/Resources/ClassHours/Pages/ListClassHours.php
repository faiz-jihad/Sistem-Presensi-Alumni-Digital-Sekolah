<?php

namespace App\Filament\Resources\ClassHours\Pages;

use App\Filament\Resources\ClassHours\ClassHourResource;
use Filament\Resources\Pages\ListRecords;

class ListClassHours extends ListRecords
{
    protected static string $resource = ClassHourResource::class;
}