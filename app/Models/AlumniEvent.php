<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlumniEvent extends Model
{
    use HasFactory;

    protected $table = 'alumni_events';

    protected $fillable = [
        'school_id',
        'posted_by',
        'title',
        'description',
        'event_date',
        'location',
        'banner_image',
        'is_active',
    ];

    protected $casts = [
        'event_date' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function postedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'posted_by');
    }
}
