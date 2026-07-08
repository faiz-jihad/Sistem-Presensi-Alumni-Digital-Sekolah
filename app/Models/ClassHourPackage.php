<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClassHourPackage extends Model
{
    use HasFactory;

    protected $table = 'class_hour_packages';

    protected $fillable = [
        'school_id',
        'name',
        'status',
    ];

    /**
     * Relasi ke School
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Relasi ke ClassHours
     */
    public function classHours(): HasMany
    {
        return $this->hasMany(ClassHour::class, 'class_hour_package_id');
    }
}
