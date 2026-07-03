<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class School extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'npsn',
        'address',
        'phone',
        'email',
        'website',
        'logo',
        'principal_name',
        'level',
        'accreditation',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    /**
     * Relasi ke semua Users di sekolah ini
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Relasi ke Students (User role = student)
     */
    public function students()
    {
        return $this->hasMany(User::class)->where('role', 'student');
    }

    /**
     * Relasi ke Teachers (User role = teacher)
     */
    public function teachers()
    {
        return $this->hasMany(User::class)->where('role', 'teacher');
    }

    /**
     * Relasi ke Alumni (User role = alumni)
     */
    public function alumni()
    {
        return $this->hasMany(User::class)->where('role', 'alumni');
    }

    /**
     * Relasi ke Admins (User role = admin)
     */
    public function admins()
    {
        return $this->hasMany(User::class)->where('role', 'admin');
    }

    /**
     * Relasi ke Classes (kelas di sekolah ini)
     */
    public function classes()
    {
        return $this->hasMany(SchoolClass::class);
    }
}