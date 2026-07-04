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
        'entry_year',
        'graduation_year_university',
        'company_name',
        'job_position',
        'industry',
        'salary_range_min',
        'salary_range_max',
        'business_name',
        'business_field',
        'business_start_year',
        'city',
        'province',
        'whatsapp',
        'linkedin_url',
        'instagram',
        'achievements',
        'testimonial',
        'is_willing_to_be_contacted',
        'last_updated_at',
    ];

    public function alumni(): BelongsTo
    {
        return $this->belongsTo(Alumni::class, 'alumni_id');
    }
}
