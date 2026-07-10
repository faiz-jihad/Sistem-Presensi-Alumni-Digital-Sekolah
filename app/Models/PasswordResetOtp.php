<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordResetOtp extends Model
{
    protected $fillable = [
        'email',
        'otp_code',
        'expires_at',
        'failed_attempts',
        'is_used',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'failed_attempts' => 'integer',
        'is_used' => 'boolean',
    ];
}
