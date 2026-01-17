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
    public function getExamScheduleByClass(int $classId)
    {
        return self::query()
            ->where('class_id', $classId)
            ->orderBy('exam_date')
            ->orderBy('exam_time')
            ->get([
                'exam_date',
                'exam_time',
                'classroom_name',
            ]);
    }
}
