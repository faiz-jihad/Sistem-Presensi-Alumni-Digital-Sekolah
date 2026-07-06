<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class QrToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'presensi_session_id',
        'token',
        'expired_at',
        'used',
    ];

    protected $casts = [
        'expired_at' => 'datetime',
        'used'       => 'boolean',
    ];

    /**
     * Relasi ke PresensiSession
     */
    public function presensiSession(): BelongsTo
    {
        return $this->belongsTo(PresensiSession::class);
    }

    /**
     * Scope: token valid (belum digunakan & belum expired)
     */
    public function scopeValid(Builder $query): Builder
    {
        return $query
            ->where('used', false)
            ->where('expired_at', '>', Carbon::now());
    }

    /**
     * Cek apakah token masih valid
     */
    public function isValid(): bool
    {
        return !$this->used && $this->expired_at->isFuture();
    }

    /**
     * Tandai token sebagai sudah digunakan
     */
    public function markAsUsed(): void
    {
        $this->update(['used' => true]);
    }
}
