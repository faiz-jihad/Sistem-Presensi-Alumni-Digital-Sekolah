<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Alumni extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'alumni';

    protected $fillable = [
        'school_id',
        'user_id',
        'nisn',
        'name',
        'gender',
        'graduation_year',
        'class_name',
        'major',
        'photo',
        'email',
        'phone',
        'verification_status',
        'verified_by',
        'verified_at',
        'verification_notes',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function profile(): HasOne
    {
        return $this->hasOne(AlumniProfile::class, 'alumni_id');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
