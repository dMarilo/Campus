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
    | Casts
    |--------------------------------------------------------------------------
    */

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'passed' => 'boolean',
        'registration_date' => 'date',
        'grade' => 'integer',
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
    public function isGraded(): bool
    {
        return $this->grade !== null;
    }

    /**
     * Get the letter grade based on numeric grade.
     *
     * @return string|null
     */
    public function getLetterGrade(): ?string
    {
        if ($this->grade === null) {
            return null;
        }

        // Adjust this mapping based on your grading system
        return match(true) {
            $this->grade >= 90 => 'A',
            $this->grade >= 80 => 'B',
            $this->grade >= 70 => 'C',
            $this->grade >= 60 => 'D',
            $this->grade >= 50 => 'E',
            default => 'F',
        };
    }

    /**
     * Get the percentage score.
     *
     * @return float|null
     */
    public function getPercentage(): ?float
    {
        if ($this->grade === null || !$this->exam) {
            return null;
        }

        if ($this->exam->max_points === 0) {
            return 0.0;
        }

        return round(($this->grade / $this->exam->max_points) * 100, 2);
    }

    /*
    |--------------------------------------------------------------------------
    | Query Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Get all exam results for a specific student.
     *
     * @param int $studentId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getResultsForStudent(int $studentId)
    {
        return self::query()
            ->where('student_id', $studentId)
            ->with(['exam.courseClass'])
            ->orderByDesc('registration_date')
            ->get();
    }

    /**
     * Get all exam results for a specific class.
     *
     * @param int $classId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getResultsForClass(int $classId)
    {
        return self::query()
            ->whereHas('exam', function ($query) use ($classId) {
                $query->where('class_id', $classId);
            })
            ->with(['exam', 'student'])
            ->get();
    }

    /**
     * Get passing exam results for a student.
     *
     * @param int $studentId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getPassedResultsForStudent(int $studentId)
    {
        return self::query()
            ->where('student_id', $studentId)
            ->where('passed', true)
            ->with(['exam.courseClass'])
            ->get();
    }

    /**
     * Scope to get only passed results.
     */
    public function scopePassed($query)
    {
        return $query->where('passed', true);
    }

    /**
     * Scope to get only failed results.
     */
    public function scopeFailed($query)
    {
        return $query->where('passed', false);
    }

    /**
     * Scope to get only graded results.
     */
    public function scopeGraded($query)
    {
        return $query->whereNotNull('grade');
    }

    /**
     * Scope to get ungraded results.
     */
    public function scopeUngraded($query)
    {
        return $query->whereNull('grade');
    }
}
