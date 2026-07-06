<?php

namespace App\Models;

use App\Enums\DayOfWeek;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'class_id',
        'subject_id',
        'teacher_id',
        'class_hour_id',
        'semester_id',
        'day',
        'room',
        'effective_start_date',
        'effective_end_date',
        'is_active',
        'allow_early_open',
    ];

    protected $casts = [
        'is_active'        => 'boolean',
        'allow_early_open' => 'boolean',
        'day'              => DayOfWeek::class,
    ];

    /* ─── Relationships ─────────────────────────── */

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function class(): BelongsTo
    {
        return $this->belongsTo(StudentClass::class, 'class_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function classHour(): BelongsTo
    {
        return $this->belongsTo(ClassHour::class);
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function presensiSessions(): HasMany
    {
        return $this->hasMany(PresensiSession::class);
    }
}
