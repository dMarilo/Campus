<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseClass extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'classes';

    /**
     * Mass-assignable attributes.
     */
    protected $fillable = [
        'course_id',
        'semester_id',
        'academic_year_id',
        'iteration',
        'status',
        'pin',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * The course this class is an instance of.
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * The semester in which the class is held.
     */
    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    /**
     * The academic year of the class.
     */
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Query helpers (optional, consistent with Course model style)
    |--------------------------------------------------------------------------
    */

    /**
     * Get all classes.
     */
    public function findAll()
    {
        return $this->newQuery()->get();
    }

    /**
     * Get a class by ID or fail.
     */
    public function findById(int $id)
    {
        return $this->newQuery()->findOrFail($id);
    }
}
