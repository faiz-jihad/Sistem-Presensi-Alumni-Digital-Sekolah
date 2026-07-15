<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Teacher extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'school_id',
        'user_id',
        'nip',
        'name',
        'gender',
        'phone',
        'address',
        'photo',
        'employment_status',
        'field_of_study',
        'education_level',
        'university',
        'join_date',
        'status',
    ];

    /* ─── Relationships ─────────────────────────── */

    protected static function booted()
    {
        static::deleted(function ($teacher) {
            $user = $teacher->user()->withTrashed()->first();
            if ($user) {
                if ($teacher->isForceDeleting()) {
                    $user->forceDelete();
                } else {
                    $user->delete();
                }
            }
        });

        static::restored(function ($teacher) {
            $teacher->user()->withTrashed()->first()?->restore();
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /** Kelas yang diampu sebagai wali kelas */
    public function homeroomClasses(): HasMany
    {
        return $this->hasMany(SchoolClass::class, 'homeroom_teacher_id');
    }

    /** Mata pelajaran yang diampu */
    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'teacher_subjects')
            ->withTimestamps();
    }

    /** Jadwal mengajar */
    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    /** Sesi presensi */
    public function presensiSessions(): HasMany
    {
        return $this->hasMany(PresensiSession::class);
    }
}