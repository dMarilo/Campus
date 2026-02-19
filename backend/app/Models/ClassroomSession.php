<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Student;
use App\Models\Attendance;
use App\Models\ClassroomSessionAttendance;
use Illuminate\Validation\ValidationException;

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

    public function attendances()
    {
        return $this->hasMany(ClassroomSessionAttendance::class);
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

    /**
     * Get all students enrolled in the class with their check-in status
     */
    public function getStudentsWithAttendance(): array
    {
        // Get all students enrolled in this class
        $attendances = Attendance::where('class_id', $this->course_class_id)
            ->with('student')
            ->get();

        // Get session attendance records
        $sessionAttendances = $this->attendances()
            ->get()
            ->keyBy('student_id');

        // Map students with their check-in status
        return $attendances->map(function ($attendance) use ($sessionAttendances) {
            $student = $attendance->student;
            $sessionAttendance = $sessionAttendances->get($student->id);

            return [
                'id' => $student->id,
                'first_name' => $student->first_name,
                'last_name' => $student->last_name,
                'code' => $student->code,
                'index_number' => $student->student_index,
                'checked_in' => $sessionAttendance !== null,
                'checked_in_at' => $sessionAttendance?->checked_in_at,
                'status' => $sessionAttendance?->status,
            ];
        })->values()->toArray();
    }

    public function checkInStudentByCode(string $studentCode): string
    {
        // 1️⃣ Session must be ongoing
        if ($this->status !== 'ongoing') {
            throw ValidationException::withMessages([
                'session' => 'Session is not active.',
            ]);
        }

        // 2️⃣ Resolve student
        $student = Student::where('code', $studentCode)->firstOrFail();

        // 3️⃣ Check enrollment
        $attendance = Attendance::where('class_id', $this->course_class_id)
            ->where('student_id', $student->id)
            ->first();

        if (!$attendance) {
            throw ValidationException::withMessages([
                'student' => 'Student is not enrolled in this class.',
            ]);
        }

        // 4️⃣ Prevent double check-in
        if ($this->attendances()
            ->where('student_id', $student->id)
            ->exists()
        ) {
            throw ValidationException::withMessages([
                'student' => 'Student already checked in.',
            ]);
        }

        $checkedInAt = now();

        $sessionStart = $this->starts_at;

        if (!$sessionStart) {
            // fallback: if start time missing, treat as present
            $status = 'present';
        } else {
            $lateAfter = $sessionStart->copy()->addMinutes(10);

            $status = $checkedInAt->gt($lateAfter)
                ? 'late'
                : 'present';
        }

        // 6️⃣ Create session attendance
        $this->attendances()->create([
            'student_id'    => $student->id,
            'checked_in_at' => $checkedInAt,
            'status'        => $status,
        ]);

        // 7️⃣ Increment overall attendance counter
        $attendance->increment('attended_sessions');

        return $status;
    }
}
