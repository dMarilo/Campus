<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | Constants
    |--------------------------------------------------------------------------
    */

    /**
     * Exam types.
     */
    public const TYPE_MIDTERM  = 'midterm';
    public const TYPE_FINAL    = 'final';
    public const TYPE_RETAKE   = 'retake';
    public const TYPE_QUIZ     = 'quiz';
    public const TYPE_ORAL     = 'oral';
    public const TYPE_LAB_EXAM = 'lab_exam';

    /**
     * Exam lifecycle statuses.
     */
    public const STATUS_PLANNED  = 'planned';
    public const STATUS_OPEN     = 'open';
    public const STATUS_CLOSED   = 'closed';
    public const STATUS_GRADED   = 'graded';
    public const STATUS_CANCELED = 'canceled';

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    /**
     * The attributes that are mass assignable.
     *
     * These fields define a concrete assessment event
     * belonging to a specific class.
     */
    protected $fillable = [
        'class_id',
        'type',
        'title',
        'exam_date',
        'exam_time',
        'classroom_name',
        'max_points',
        'status',
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
        'exam_date' => 'date',
        'exam_time' => 'datetime:H:i',
        'max_points' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * The class this exam belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function courseClass()
    {
        return $this->belongsTo(CourseClass::class, 'class_id');
    }

    /**
     * The exam results for this exam.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function examResults()
    {
        return $this->hasMany(ExamResult::class);
    }

    /**
     * Students who have taken this exam.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function students()
    {
        return $this->belongsToMany(Student::class, 'exam_results')
            ->withPivot('grade', 'passed', 'registration_date')
            ->withTimestamps();
    }

    /*
    |--------------------------------------------------------------------------
    | Domain Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Determine whether the exam is currently open.
     *
     * @return bool
     */
    public function isOpen(): bool
    {
        return $this->status === self::STATUS_OPEN;
    }

    /**
     * Determine whether the exam has been graded.
     *
     * @return bool
     */
    public function isGraded(): bool
    {
        return $this->status === self::STATUS_GRADED;
    }

    /**
     * Determine whether a student can take this exam.
     *
     * @return bool
     */
    public function canBeTaken(): bool
    {
        return in_array($this->status, [self::STATUS_PLANNED, self::STATUS_OPEN]);
    }

    /**
     * Determine whether the exam is in the past.
     *
     * @return bool
     */
    public function isPast(): bool
    {
        $examDateTime = $this->exam_date->setTimeFromTimeString($this->exam_time);
        return $examDateTime->isPast();
    }

    /**
     * Determine whether the exam is upcoming.
     *
     * @return bool
     */
    public function isUpcoming(): bool
    {
        $examDateTime = $this->exam_date->setTimeFromTimeString($this->exam_time);
        return $examDateTime->isFuture();
    }

    /**
     * Open the exam.
     *
     * Used when students are allowed to participate.
     *
     * @return bool
     */
    public function open(): bool
    {
        return $this->update([
            'status' => self::STATUS_OPEN,
        ]);
    }

    /**
     * Close the exam.
     *
     * Used when the exam session ends.
     *
     * @return bool
     */
    public function close(): bool
    {
        return $this->update([
            'status' => self::STATUS_CLOSED,
        ]);
    }

    /**
     * Mark the exam as graded.
     *
     * Typically used after all results are entered.
     *
     * @return bool
     */
    public function markAsGraded(): bool
    {
        return $this->update([
            'status' => self::STATUS_GRADED,
        ]);
    }

    /**
     * Cancel the exam.
     *
     * @return bool
     */
    public function cancel(): bool
    {
        return $this->update([
            'status' => self::STATUS_CANCELED,
        ]);
    }

    /**
     * Check if a student has already taken this exam.
     *
     * @param int $studentId
     * @return bool
     */
    public function hasStudentTaken(int $studentId): bool
    {
        return $this->examResults()
            ->where('student_id', $studentId)
            ->exists();
    }

    /**
     * Get the exam result for a specific student.
     *
     * @param int $studentId
     * @return ExamResult|null
     */
    public function getStudentResult(int $studentId): ?ExamResult
    {
        return $this->examResults()
            ->where('student_id', $studentId)
            ->first();
    }

    /**
     * Calculate the pass rate for this exam.
     *
     * @return float
     */
    public function getPassRate(): float
    {
        $total = $this->examResults()->count();

        if ($total === 0) {
            return 0.0;
        }

        $passed = $this->examResults()->where('passed', true)->count();

        return round(($passed / $total) * 100, 2);
    }

    /**
     * Get the average grade for this exam.
     *
     * @return float
     */
    public function getAverageGrade(): float
    {
        $average = $this->examResults()
            ->whereNotNull('grade')
            ->avg('grade');

        return $average ? round($average, 2) : 0.0;
    }

    /*
    |--------------------------------------------------------------------------
    | Query Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Get all exam dates for a given class.
     *
     * Returns a collection of dates on which exams
     * are scheduled for the specified class.
     *
     * @param int $classId
     * @return \Illuminate\Support\Collection
     */
    public static function getExamScheduleByClass(int $classId)
    {
        return self::query()
            ->where('class_id', $classId)
            ->with('courseClass.course')
            ->orderBy('exam_date')
            ->orderBy('exam_time')
            ->get();
    }

    /**
     * Get all exams for a specific student.
     *
     * @param int $studentId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getExamsForStudent(int $studentId)
    {
        return self::query()
            ->whereHas('courseClass.attendances', function ($query) use ($studentId) {
                $query->where('student_id', $studentId);
            })
            ->with(['courseClass.course', 'examResults' => function ($query) use ($studentId) {
                $query->where('student_id', $studentId);
            }])
            ->orderBy('exam_date')
            ->orderBy('exam_time')
            ->get();
    }

    /**
     * Scope to get only upcoming exams.
     */
    public function scopeUpcoming($query)
    {
        return $query->where('exam_date', '>=', now()->toDateString())
            ->orderBy('exam_date')
            ->orderBy('exam_time');
    }

    /**
     * Scope to get only past exams.
     */
    public function scopePast($query)
    {
        return $query->where('exam_date', '<', now()->toDateString())
            ->orderByDesc('exam_date')
            ->orderByDesc('exam_time');
    }

    /**
     * Scope to filter by status.
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter by type.
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }
}
