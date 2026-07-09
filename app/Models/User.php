<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles; // Tambahkan ini
use NotificationChannels\WebPush\HasPushSubscriptions;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens,
        HasFactory,
        Notifiable,
        SoftDeletes,
        HasRoles,
        HasPushSubscriptions; // Tambahkan HasPushSubscriptions

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role', // super_admin, admin, teacher, student, alumni
        'school_id',
        'status',
        'google_id',
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
     * Relasi ke Teacher
     */
    public function teacher(): HasOne
    {
        return $this->hasOne(Teacher::class, 'user_id');
    }


    /**
     * Relasi ke Student (sebagai orang tua/parent)
     */
    public function student()
    {
        return $this->hasOne(Student::class, 'parent_user_id');
    }

    /**
     * Relasi ke Alumni
     */
    public function alumni()
    {
        return $this->hasOne(Alumni::class, 'user_id');
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
        if (! in_array($this->role, [
            'super_admin',
            'admin',
            'teacher',
            'alumni',
        ])) {
            return false;
        }

        if ($this->role === 'super_admin') {
            return true;
        }

        if (!$this->school) {
            return false;
        }

        return true;
    }

    /**
     * Cek apakah user memiliki akses ke fitur berdasarkan paket sekolahnya
     */
    public function hasFeature(string $feature): bool
    {
        if ($this->role === 'super_admin') {
            return true;
        }

        if (!$this->isSchoolActive()) {
            return false;
        }

        if (!$this->school_id) {
            return false;
        }

        $school = $this->school;
        if (!$school) {
            return false;
        }

        // Jika sekolah tidak diset paketnya, default izinkan semua fitur
        if (!$school->package_id) {
            return true;
        }

        $package = $school->package;
        if (!$package) {
            return true;
        }

        return (bool) $package->{$feature};
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

    public function isSchoolActive(): bool
    {
        // Super Admin selalu dianggap aktif
        if ($this->role === 'super_admin') {
            return true;
        }

        // Jika tidak memiliki sekolah
        if (! $this->school) {
            return false;
        }

        return $this->school->status === 'active';
    }

    public function fcmTokens(): HasMany
    {
        return $this->hasMany(FcmToken::class);
    }
}
