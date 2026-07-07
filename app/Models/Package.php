<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'duration_months',
        'is_active',
        'has_presensi',
        'has_alumni',
        'has_tracer_study',
        'has_job_vacancy',
        'has_export',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'has_presensi' => 'boolean',
        'has_alumni' => 'boolean',
        'has_tracer_study' => 'boolean',
        'has_job_vacancy' => 'boolean',
        'has_export' => 'boolean',
    ];

    /**
     * Relasi ke Sekolah yang menggunakan paket ini.
     */
    public function schools(): HasMany
    {
        return $this->hasMany(School::class);
    }

    /**
     * Scope: Hanya paket yang aktif.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
