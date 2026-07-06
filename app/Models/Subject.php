<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subject extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    /* ─── Relationships ─────────────────────────── */

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /** Guru yang mengampu mata pelajaran ini */
    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(Teacher::class, 'teacher_subjects')
            ->withTimestamps();
    }

    /** Jadwal pelajaran yang menggunakan mata pelajaran ini */
    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }
}
