<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teaching extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'teaching';

    /**
     * Mass-assignable attributes.
     */
    protected $fillable = [
        'professor_id',
        'class_id',
        'role',
        'status',
        'taught_sessions',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * The professor teaching the class.
     */
    public function professor()
    {
        return $this->belongsTo(Professor::class);
    }

    /**
     * The class being taught.
     */
    public function courseClass()
    {
        return $this->belongsTo(CourseClass::class, 'class_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Query helpers (optional, but consistent)
    |--------------------------------------------------------------------------
    */

    /**
     * Get all professors teaching a specific class.
     *
     * @param int $classId
     * @return \Illuminate\Support\Collection
     */
    public function getProfessorsByClass(int $classId)
    {
        return $this->newQuery()
            ->with('professor')
            ->where('class_id', $classId)
            ->get()
            ->pluck('professor');
    }

    /**
     * Get all classes taught by a specific professor.
     *
     * @param int $professorId
     * @return \Illuminate\Support\Collection
     */
    public function getClassesByProfessor(int $professorId)
    {
        return $this->newQuery()
            ->with([
                'courseClass.course',
                'courseClass.semester',
                'courseClass.academicYear',
            ])
            ->where('professor_id', $professorId)
            ->get()
            ->pluck('courseClass');
    }
}
