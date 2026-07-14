<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'settings';

    protected $fillable = [
        'key',
        'value',
        'group',
    ];

    /**
     * Dapatkan nilai setting berdasarkan key.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = self::where('key', strtolower($key))->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Simpan/update setting.
     */
    public static function set(string $key, mixed $value, string $group = 'general'): void
    {
        self::updateOrCreate(
            ['key' => strtolower($key)],
            ['value' => $value, 'group' => $group]
        );
    }
}
