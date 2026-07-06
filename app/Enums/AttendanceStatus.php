<?php

namespace App\Enums;

enum AttendanceStatus: string
{
    case Present    = 'present';
    case Late       = 'late';
    case Permission = 'permission';
    case Sick       = 'sick';
    case Absent     = 'absent';

    public function label(): string
    {
        return match ($this) {
            self::Present    => 'Hadir',
            self::Late       => 'Terlambat',
            self::Permission => 'Izin',
            self::Sick       => 'Sakit',
            self::Absent     => 'Alpha',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Present    => 'success',
            self::Late       => 'warning',
            self::Permission => 'info',
            self::Sick       => 'primary',
            self::Absent     => 'danger',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(
            fn (self $case) => [$case->value => $case->label()]
        )->toArray();
    }
}
