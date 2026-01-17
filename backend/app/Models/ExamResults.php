<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamResult extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    /**
     * The attributes that are mass assignable.
     *
     * These fields represent the official, registered
     * outcome of a student's exam attempt.
     */
    protected $fillable = [
        'exam_id',
        'student_id',
        'grade',
        'passed',
        'registration_date',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * The exam this result belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    /**
     * The student this exam result belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Domain Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Register a grade for this exam result.
     *
     * This method encapsulates the business logic
     * of grading and automatically determines
     * whether the student has passed.
     *
     * @param int $grade
     * @param bool $passed
     * @param \DateTimeInterface|null $registrationDate
     * @return bool
     */
    public function registerGrade(
        int $grade,
        bool $passed,
        \DateTimeInterface $registrationDate = null
    ): bool {
        return $this->update([
            'grade' => $grade,
            'passed' => $passed,
            'registration_date' => $registrationDate
                ? $registrationDate->format('Y-m-d')
                : now()->toDateString(),
        ]);
    }

    /**
     * Determine whether the student has passed the exam.
     *
     * @return bool
     */
    public function hasPassed(): bool
    {
        return $this->passed === true;
    }

    /**
     * Determine whether the exam has been graded.
     *
     * @return bool
     */
    public function isRegistered(): bool
    {
        return $this->grade !== null;
    }
}
