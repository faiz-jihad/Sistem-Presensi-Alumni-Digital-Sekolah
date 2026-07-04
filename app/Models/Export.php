<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Export extends Model
{
    protected $fillable = [
        'school_id',
        'created_by',
        'type',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'filters',
        'status',
        'error_message',
        'completed_at',
    ];

    protected $casts = [
        'filters' => 'array',
        'completed_at' => 'datetime',
        'school_id' => 'integer',
        'created_by' => 'integer',
        'file_size' => 'integer',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    protected static function booted(): void
    {
        static::deleted(function (Export $export) {
            if ($export->file_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($export->file_path)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($export->file_path);
            }
        });
    }
}
