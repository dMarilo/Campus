<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class ClassroomSessionAttendance extends Model
{
    protected $fillable = [
        'classroom_session_id',
        'student_id',
        'checked_in_at',
        'status',
    ];

    public function session()
    {
        return $this->belongsTo(ClassroomSession::class, 'classroom_session_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
