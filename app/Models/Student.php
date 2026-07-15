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
        'parent_phone',
        'nis',
        'nisn',
        'name',
        'gender',
        'birth_date',
        'status',
    ];

    protected static function booted()
    {
        static::deleted(function ($student) {
            // Find student's own login user record
            $studentUser = \App\Models\User::withTrashed()
                ->where('role', 'student')
                ->where(function ($q) use ($student) {
                    $q->where('email', $student->nis)
                      ->orWhere('name', $student->name);
                })->first();

            if ($studentUser) {
                if ($student->isForceDeleting()) {
                    $studentUser->forceDelete();
                } else {
                    $studentUser->delete();
                }
            }

            // Find parent user and delete if orphaned (i.e. has no other active students)
            if ($student->parent_user_id) {
                $parent = $student->parent()->withTrashed()->first();
                if ($parent) {
                    $hasOtherStudents = self::where('parent_user_id', $student->parent_user_id)
                        ->where('id', '!=', $student->id)
                        ->exists();

                    if (!$hasOtherStudents) {
                        if ($student->isForceDeleting()) {
                            $parent->forceDelete();
                        } else {
                            $parent->delete();
                        }
                    }
                }
            }
        });

        static::restored(function ($student) {
            // Restore student's own user record
            $studentUser = \App\Models\User::onlyTrashed()
                ->where('role', 'student')
                ->where(function ($q) use ($student) {
                    $q->where('email', $student->nis)
                      ->orWhere('name', $student->name);
                })->first();

            if ($studentUser) {
                $studentUser->restore();
            }

            // Restore parent user if soft-deleted
            if ($student->parent_user_id) {
                $parent = $student->parent()->onlyTrashed()->first();
                if ($parent) {
                    $parent->restore();
                }
            }
        });
    }

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