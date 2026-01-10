<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    use HasFactory;

    /**
     * Mass-assignable attributes for the Semester model.
     * Represents the organizational and chronological properties
     * of an academic semester.
     */
    protected $fillable = [
        'name',
        'code',
        'order',
    ];

    /**
     * Defines the relationship between a semester and its academic year.
     * Each semester belongs to a single academic year.
     */
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }
}
