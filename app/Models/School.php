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
        'address',
        'phone',
        'email',
        'website',
        'logo',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * Relasi ke User
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Relasi ke Student (User dengan role student)
     */
    public function students()
    {
        return $this->hasMany(User::class)->where('role', 'student');
    }

    /**
     * Relasi ke Alumni (User dengan role alumni)
     */
    public function alumni()
    {
        return $this->hasMany(User::class)->where('role', 'alumni');
    }

    /**
     * Relasi ke Teacher (User dengan role teacher)
     */
    public function teachers()
    {
        return $this->hasMany(User::class)->where('role', 'teacher');
    }

    /**
     * Relasi ke Admin (User dengan role admin)
     */
    public function admins()
    {
        return $this->hasMany(User::class)->where('role', 'admin');
    }
}