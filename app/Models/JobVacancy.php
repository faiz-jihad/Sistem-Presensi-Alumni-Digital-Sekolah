<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobVacancy extends Model
{
    use HasFactory;

    protected $table = 'job_vacancies';

    protected $fillable = [
    'school_id',
    'posted_by',
    'title',
    'company_name',
    'company_logo',
    'description',
    'requirements',
    'link',
    'location',
    'salary_min',
    'salary_max',
    'job_type',
    'category',
    'deadline',
    'is_active',
    ];

    protected $casts = [
    'deadline' => 'date',
    'salary_min' => 'integer',
    'salary_max' => 'integer',
    'is_active' => 'boolean',
    ];

    // Relasi ke user yang posting
    public function postedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    // Relasi ke sekolah
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    // Scope untuk lowongan aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('deadline')
                  ->orWhere('deadline', '>=', now());
            });
    }

    // Scope berdasarkan tipe
    public function scopeJobType($query, $type)
    {
        return $query->where('job_type', $type);
    }

    // Accessor untuk label job type
    public function getJobTypeLabelAttribute(): string
    {
        return match($this->job_type) {
            'full_time' => 'Full Time',
            'part_time' => 'Part Time',
            'freelance' => 'Freelance',
            'internship' => 'Magang',
            default => $this->job_type,
        };
    }

    // Accessor untuk label kategori
    public function getCategoryLabelAttribute(): string
    {
        return match($this->category) {
            'technology' => 'Teknologi',
            'education' => 'Pendidikan',
            'health' => 'Kesehatan',
            'business' => 'Bisnis',
            'creative' => 'Kreatif',
            'engineering' => 'Teknik',
            'others' => 'Lainnya',
            default => $this->category,
        };
    }

    // Accessor untuk format gaji
    public function getSalaryFormattedAttribute(): string
    {
        if ($this->salary_min && $this->salary_max) {
            return 'Rp ' . number_format($this->salary_min, 0, ',', '.') . 
                   ' - Rp ' . number_format($this->salary_max, 0, ',', '.');
        } elseif ($this->salary_min) {
            return 'Rp ' . number_format($this->salary_min, 0, ',', '.') . ' - ...';
        } elseif ($this->salary_max) {
            return '... - Rp ' . number_format($this->salary_max, 0, ',', '.');
        }
        return 'Tidak disebutkan';
    }
}