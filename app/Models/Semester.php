<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    protected $guarded = ['id'];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }
}
