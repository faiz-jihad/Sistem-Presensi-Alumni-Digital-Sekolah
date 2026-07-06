<?php

namespace App\Enums;

enum DayOfWeek: string
{
    case Monday    = 'monday';
    case Tuesday   = 'tuesday';
    case Wednesday = 'wednesday';
    case Thursday  = 'thursday';
    case Friday    = 'friday';
    case Saturday  = 'saturday';
    case Sunday    = 'sunday';

    public function label(): string
    {
        return match ($this) {
            self::Monday    => 'Senin',
            self::Tuesday   => 'Selasa',
            self::Wednesday => 'Rabu',
            self::Thursday  => 'Kamis',
            self::Friday    => 'Jumat',
            self::Saturday  => 'Sabtu',
            self::Sunday    => 'Minggu',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(
            fn (self $case) => [$case->value => $case->label()]
        )->toArray();
    }

    public static function fromCarbon(\Carbon\Carbon $carbon): self
    {
        return self::from(strtolower($carbon->format('l')));
    }
}
