<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Nama tabel
     */
    protected $table = 'students';

    /**
     * Kolom yang bisa diisi (mass assignment)
     */
    protected $fillable = [
        'school_id',
        'class_id',
        'parent_user_id',
        'nis',
        'nisn',
        'name',
        'gender',
        'birth_date',
        'birth_place',
        'address',
        'photo',
        'parent_name',
        'parent_phone',
        'enrollment_year',
        'status',
    ];

    /**
     * Relasi ke School
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Relasi ke Class
     */
    public function class()
    {
        return $this->belongsTo(StudentClass::class, 'class_id');
    }

    /**
     * Relasi ke Parent (User)
     */
    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_user_id');
    }

    /**
     * Relasi ke Attendances
     */
    public function attendances()
    {
        return $this->hasMany(StudentAttendance::class);
    }
}