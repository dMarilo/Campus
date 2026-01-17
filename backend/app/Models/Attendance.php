<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'attendance';

    /**
     * Mass-assignable attributes.
     */
    protected $fillable = [
        'student_id',
        'class_id',
        'status',
        'attended_sessions',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * The student attending the class.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * The class being attended.
     */
    public function courseClass()
    {
        return $this->belongsTo(CourseClass::class, 'class_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Query helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Get all students attending a specific class.
     *
     * @param int $classId
     * @return \Illuminate\Support\Collection
     */
    public function getStudentsByClass(int $classId)
    {
        return $this->newQuery()
            ->with('student')
            ->where('class_id', $classId)
            ->get()
            ->pluck('student');
    }

    /**
     * Get all classes attended by a specific student.
     *
     * @param int $studentId
     * @return \Illuminate\Support\Collection
     */
    public function getClassesByStudent(int $studentId)
    {
        return $this->newQuery()
            ->with([
                'courseClass.course',
                'courseClass.semester',
                'courseClass.academicYear',
            ])
            ->where('student_id', $studentId)
            ->get()
            ->pluck('courseClass');
    }
}
