<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | Constants
    |--------------------------------------------------------------------------
    */

    public const STATUS_ACTIVE    = 'active';
    public const STATUS_GRADUATED = 'graduated';
    public const STATUS_SUSPENDED = 'suspended';

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'phone',
        'date_of_birth',
        'gender',
        'student_index',
        'code',
        'year_of_study',
        'department',
        'gpa',
        'status',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected $casts = [
        'date_of_birth' => 'date',
        'gpa' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function fullName(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

}
