<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlumniProfile extends Model
{
    use HasFactory;

    protected $table = 'alumni_profiles';

    protected $fillable = [
        'alumni_id',
        'current_status',
        'university_name',
        'study_program',
        'company_name',
        'job_position',
        'business_name',
        'city',
        'province',
        'whatsapp',
        'linkedin_url',
    ];

    public function alumni(): BelongsTo
    {
        return $this->belongsTo(Alumni::class, 'alumni_id');
    }
}
