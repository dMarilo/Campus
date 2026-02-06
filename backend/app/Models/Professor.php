<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Professor extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | Constants
    |--------------------------------------------------------------------------
    */

    /**
     * Professor lifecycle statuses.
     */
    public const STATUS_ACTIVE   = 'active';
    public const STATUS_INACTIVE = 'inactive';

    /**
     * Employment types.
     */
    public const EMPLOYMENT_FULL_TIME = 'full_time';
    public const EMPLOYMENT_PART_TIME = 'part_time';
    public const EMPLOYMENT_EXTERNAL  = 'external';

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'code',
        'first_name',
        'last_name',
        'email',
        'phone',
        'academic_title',
        'department',
        'employment_type',
        'status',
        'office_location',
        'office_hours',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * The user account associated with the professor.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Classes taught by the professor.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function teaching()
    {
        return $this->hasMany(Teaching::class);
    }

    /*
    |--------------------------------------------------------------------------
    | CRUD Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Retrieve all professors.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllProfessors()
    {
        return self::all();
    }

    /**
     * Retrieve a professor by ID.
     *
     * @param int $id
     * @return \App\Models\Professor
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getProfessorById(int $id): Professor
    {
        return self::findOrFail($id);
    }

    /**
     * Retrieve a professor by their unique code.
     *
     * @param string $code
     * @return \App\Models\Professor
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getProfessorByCode(string $code): Professor
    {
        return self::where('code', $code)->firstOrFail();
    }

    /**
     * Search professors by name (partial match).
     *
     * @param string $searchTerm
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function searchProfessorsByName(string $searchTerm)
    {
        return self::where('first_name', 'LIKE', "%{$searchTerm}%")
            ->orWhere('last_name', 'LIKE', "%{$searchTerm}%")
            ->get();
    }

    /**
     * Retrieve professors by department.
     *
     * @param string $department
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getProfessorsByDepartment(string $department)
    {
        return self::where('department', $department)->get();
    }

    /**
     * Retrieve only active professors.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveProfessors()
    {
        return self::where('status', self::STATUS_ACTIVE)->get();
    }

    /**
     * Create and store a new professor.
     *
     * @param array $data
     * @return \App\Models\Professor
     */
    public function loadProfessor(array $data): Professor
    {
        return self::create($data);
    }

    /**
     * Update an existing professor.
     *
     * @param int   $id
     * @param array $data
     * @return \App\Models\Professor
     */
    public function updateProfessor(int $id, array $data): Professor
    {
        $professor = self::findOrFail($id);
        $professor->update($data);
        return $professor->fresh();
    }

    /**
     * Delete a professor.
     *
     * @param int $id
     * @return bool|null
     */
    public function deleteProfessor(int $id)
    {
        return self::findOrFail($id)->delete();
    }

    /*
    |--------------------------------------------------------------------------
    | Domain Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Determine whether the professor is currently active.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Get the professor's full name.
     *
     * @return string
     */
    public function fullName(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Activate the professor.
     *
     * @return bool
     */
    public function activate(): bool
    {
        return $this->update([
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Deactivate the professor.
     *
     * @return bool
     */
    public function deactivate(): bool
    {
        return $this->update([
            'status' => self::STATUS_INACTIVE,
        ]);
    }

    /**
     * Determine whether the professor is employed full-time.
     *
     * @return bool
     */
    public function isFullTime(): bool
    {
        return $this->employment_type === self::EMPLOYMENT_FULL_TIME;
    }

    /**
     * Determine whether the professor is employed part-time.
     *
     * @return bool
     */
    public function isPartTime(): bool
    {
        return $this->employment_type === self::EMPLOYMENT_PART_TIME;
    }

    /**
     * Determine whether the professor is an external associate.
     *
     * @return bool
     */
    public function isExternal(): bool
    {
        return $this->employment_type === self::EMPLOYMENT_EXTERNAL;
    }
}
