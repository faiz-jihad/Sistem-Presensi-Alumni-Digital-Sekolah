<?php

namespace App\Models;

use App\Enums\SessionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PresensiSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'class_id',
        'schedule_id',
        'teacher_id',
        'opened_by',
        'opened_at',
        'date',
        'start_time',
        'end_time',
        'status',
        'attendance_method',
        'qr_token',
        'material_topic',
        'notes',
        'latitude',
        'longitude',
        'closed_latitude',
        'closed_longitude',
        'photo',
        'is_late',
        'closed_by',
        'closed_at',
    ];

    protected $casts = [
        'status'    => SessionStatus::class,
        'closed_at' => 'datetime',
        'opened_at' => 'datetime',
        'is_late'   => 'boolean',
    ];

    /* ─── Relationships ─────────────────────────── */

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    public function class(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    /** User yang membuka sesi */
    public function openedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'opened_by');
    }

    /** User yang menutup sesi */
    public function closedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function studentAttendances(): HasMany
    {
        return $this->hasMany(StudentAttendance::class, 'presensi_session_id');
    }

    public function qrTokens(): HasMany
    {
        return $this->hasMany(QrToken::class);
    }

    /* ─── Helpers ─────────────────────────────── */

    public function isOpen(): bool
    {
        return $this->status === SessionStatus::Open;
    }

    public function isClosed(): bool
    {
        return $this->status === SessionStatus::Closed;
    }
}
