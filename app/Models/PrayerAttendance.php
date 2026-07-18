<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrayerAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'class_id',
        'student_id',
        'prayer_type',
        'attendance_date',
        'scheduled_at',
        'submitted_at',
        'status',
        'verified_by',
        'verified_at',
        'teacher_note',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'submitted_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function studentClass(): BelongsTo
    {
        return $this->belongsTo(StudentClass::class, 'class_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    protected function scheduledAt(): Attribute
    {
        return Attribute::get(
            fn (?string $value): ?string => $value === null ? null : substr($value, 0, 5)
        );
    }
}
