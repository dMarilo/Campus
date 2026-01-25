<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClassroomSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'classroom_id',
        'course_class_id',
        'starts_at',
        'ends_at',
        'status',
    ];

    protected $casts = [
        'starts_at' => 'datetime'
    ];

    /* -----------------------------------------------------------------
     |  Relationships
     | -----------------------------------------------------------------
     */

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function courseClass()
    {
        return $this->belongsTo(CourseClass::class);
    }

    public function professor()
    {
        return $this->belongsTo(Professor::class);
    }

    /* -----------------------------------------------------------------
     |  State helpers
     | -----------------------------------------------------------------
     */

    public function isOngoing(): bool
    {
        return $this->status === 'ongoing';
    }

    public function isFinished(): bool
    {
        return $this->status === 'finished';
    }

    public function isScheduled(): bool
    {
        return $this->status === 'scheduled';
    }
}
