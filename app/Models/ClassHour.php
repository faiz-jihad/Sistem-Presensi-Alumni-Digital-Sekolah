<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClassHour extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'class_hour_package_id',
        'code',
        'start_time',
        'end_time',
        'duration_minutes',
        'order',
        'is_break',
        'shift',
        'status',
    ];

    protected $casts = [
        'is_break' => 'boolean',
    ];

    /**
     * Relasi ke School
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Relasi ke ClassHourPackage
     */
    public function classHourPackage(): BelongsTo
    {
        return $this->belongsTo(ClassHourPackage::class, 'class_hour_package_id');
    }
}
