<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relasi ke School
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Relasi ke Kelas (StudentClass)
     */
    public function class(): BelongsTo
    {
        return $this->belongsTo(StudentClass::class, 'class_id');
    }

    /**
     * Relasi ke Mata Pelajaran (Subject)
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Relasi ke Guru (Teacher)
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    /**
     * Relasi ke Jam Pelajaran (ClassHour)
     */
    public function classHour(): BelongsTo
    {
        return $this->belongsTo(ClassHour::class);
    }

    /**
     * Relasi ke Semester
     */
    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }
}
