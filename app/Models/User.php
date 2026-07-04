<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles; // Tambahkan ini

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens,
        HasFactory,
        Notifiable,
        SoftDeletes,
        HasRoles; // Tambahkan HasRoles

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role', // super_admin, admin, teacher, student, alumni
        'school_id',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Relasi ke School
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Role yang dikelola oleh Spatie Permission (hanya untuk akses Filament panel)
     */
    private const SPATIE_ROLES = ['super_admin', 'admin', 'teacher'];

    /**
     * Auto sync role dari kolom 'role' ke Spatie Permission
     * Hanya untuk role Filament: super_admin, admin, teacher
     */
    protected static function booted()
    {
        static::created(function ($user) {
            if ($user->role && in_array($user->role, self::SPATIE_ROLES)) {
                $user->assignRole($user->role);
            }
        });

        static::updated(function ($user) {
            if ($user->isDirty('role') && in_array($user->role, self::SPATIE_ROLES)) {
                $user->syncRoles([$user->role]);
            } elseif ($user->isDirty('role') && !in_array($user->role, self::SPATIE_ROLES)) {
                // Hapus spatie role jika role berubah ke non-Filament
                $user->syncRoles([]);
            }
        });
    }

    /**
     * WAJIB: Method ini harus return TRUE agar bisa akses Filament
     * Dengan pengecekan role
     */
    public function canAccessPanel(Panel $panel): bool
    {
        // Hanya user dengan role tertentu yang bisa akses Filament
        $allowedRoles = ['super_admin', 'admin', 'teacher'];
        return in_array($this->role, $allowedRoles);
    }

    /**
     * Helper methods untuk cek role
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isTeacher(): bool
    {
        return $this->role === 'teacher';
    }

    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    public function isAlumni(): bool
    {
        return $this->role === 'alumni';
    }

    /**
     * Scope untuk filter by role
     */
    public function scopeSuperAdmin($query)
    {
        return $query->where('role', 'super_admin');
    }

    public function scopeAdmin($query)
    {
        return $query->where('role', 'admin');
    }

    public function scopeTeacher($query)
    {
        return $query->where('role', 'teacher');
    }

    public function scopeStudent($query)
    {
        return $query->where('role', 'student');
    }

    public function scopeAlumni($query)
    {
        return $query->where('role', 'alumni');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}