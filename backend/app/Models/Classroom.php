<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use App\Models\ClassroomSession;
use App\Models\Professor;
use App\Models\SchoolClass;

class Classroom extends Model
{
    use HasFactory;

    /**
     * Mass-assignable attributes
     */
    protected $fillable = [
        'building_id',
        'name',
        'capacity',
        'type',
        'status',
        'active_session_id',
    ];

    /**
     * Attribute casting
     */
    protected $casts = [
        'capacity' => 'integer',
    ];

    /* -----------------------------------------------------------------
     |  Relationships
     | -----------------------------------------------------------------
     */

    /**
     * Classroom belongs to a building
     */
    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    /**
     * Currently active session (if occupied)
     */
    // public function activeSession()
    // {
    //     return $this->belongsTo(ClassroomSession::class, 'active_session_id');
    // }

    /**
     * All sessions held in this classroom
     */
    public function sessions()
    {
        return $this->hasMany(ClassroomSession::class);
    }

    /* -----------------------------------------------------------------
     |  State helpers (VERY useful)
     | -----------------------------------------------------------------
     */

    public function isEmpty(): bool
    {
        return $this->status === 'empty';
    }

    public function isOccupied(): bool
    {
        return $this->status === 'occupied';
    }

    public function isReserved(): bool
    {
        return $this->status === 'reserved';
    }



    /**
     * Query for all classrooms with relations
     */
    public function scopeAllClassrooms($query)
    {
        return $query
            ->with('building')
            ->orderBy('name');
    }












        /**
     * Start a classroom session
     *
     * @throws ValidationException
     */
    public function startSession(string $classPin, string $professorCode): ClassroomSession
    {
        if ($this->status !== 'empty') {
            throw ValidationException::withMessages([
                'classroom' => 'Classroom is not available.',
            ]);
        }

        return DB::transaction(function () use ($classPin, $professorCode) {

            // 1️⃣ Resolve class by PIN
            $class = CourseClass::where('pin', $classPin)->first();

            if (!$class) {
                throw ValidationException::withMessages([
                    'pin' => 'Invalid class PIN.',
                ]);
            }

            if (!in_array($class->status, ['planned', 'active'])) {
                throw ValidationException::withMessages([
                    'class' => 'Class cannot be started.',
                ]);
            }

            // 2️⃣ Resolve professor
            $professor = Professor::where('code', $professorCode)->first();

            if (!$professor) {
                throw ValidationException::withMessages([
                    'professor' => 'Invalid professor code.',
                ]);
            }

            // 3️⃣ Verify professor is assigned to this class via teaching table
            $teaching = Teaching::where('class_id', $class->id)
                ->where('professor_id', $professor->id)
                ->whereIn('status', ['assigned'])
                ->first();

            if (!$teaching) {
                throw ValidationException::withMessages([
                    'professor' => 'Professor is not authorized to teach this class.',
                ]);
            }

            // 4️⃣ Create session
            $session = ClassroomSession::create([
                'classroom_id'    => $this->id,
                'course_class_id' => $class->id,
                'professor_id'    => $professor->id,
                'starts_at'       => Carbon::now(),
                'status'          => 'ongoing',
            ]);

            // 5️⃣ Update classroom state
            $this->update([
                'status'             => 'occupied',
                'active_session_id'  => $session->id,
            ]);

            // 6️⃣ Update class state (optional but recommended)
            $class->increment('iteration');
            $class->update([
                'status' => 'active',
            ]);

            return $session;
        });
    }

    public function endSession(): ClassroomSession
    {
        $session = $this->sessions()
            ->where('status', 'ongoing')
            ->firstOrFail();

        $session->update([
            'status'   => 'finished',
        ]);

        $this->update([
            'status' => 'empty',
        ]);

        return $session;
    }

}
