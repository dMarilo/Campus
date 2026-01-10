<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    use HasFactory;

    /**
     * Mass-assignable attributes for the AcademicYear model.
     * Represents the academic calendar span and its current activity status.
     */
    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'is_active',
    ];

    /**
     * Defines the relationship between an academic year and its semesters.
     * A single academic year consists of multiple semesters.
     */
    public function semesters()
    {
        return $this->hasMany(Semester::class);
    }
}
