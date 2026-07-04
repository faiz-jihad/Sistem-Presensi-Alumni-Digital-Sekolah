<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'class_id',
        'student_id',
        'teacher_id',
        'date',
        'check_in_time',
        'check_out_time',
        'status',
        'note',
        'attachment',
        'verification_status',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    /**
     * Relasi ke School
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Relasi ke Kelas (StudentClass)
     */
    public function class(): BelongsTo
    {
        return $this->belongsTo(StudentClass::class, 'class_id');
    }

    /**
     * Relasi ke Siswa (Student)
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Relasi ke Guru (Teacher)
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    /**
     * Relasi ke User yang memverifikasi izin/sakit
     */
    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
