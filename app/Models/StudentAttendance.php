<?php

namespace App\Models;

use App\Enums\AttendanceStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'class_id',
        'student_id',
        'teacher_id',
        'presensi_session_id',
        'date',
        'status',
        'check_in_time',
        'check_out_time',
        'note',
        'attachment',
        'verification_status',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'status'      => AttendanceStatus::class,
        'verified_at' => 'datetime',
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

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function presensiSession(): BelongsTo
    {
        return $this->belongsTo(PresensiSession::class, 'presensi_session_id');
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
