<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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

    // /**
    //  * All sessions held in this classroom
    //  */
    // public function sessions()
    // {
    //     return $this->hasMany(ClassroomSession::class);
    // }

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
}
