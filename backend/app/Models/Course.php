<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Course extends Model
{
    use HasFactory;

    /**
     * Mass-assignable attributes for the Course model.
     * Represents the academic definition and administrative properties
     * of a university course.
     */
    protected $fillable = [
        'code',
        'name',
        'description',
        'ects',
        'department',
        'level',
        'mandatory',
        'status',
    ];


    public function books()
    {
        return $this->belongsToMany(\App\Models\Book::class,'course_book')->withPivot('mandatory')
        ->withTimestamps();
    }



    /**
     * Retrieves all courses from the database.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findAll()
    {
        return $this->newQuery()->get();
    }

    /**
     * Retrieves a course by its unique identifier.
     * Throws an exception if the course does not exist.
     *
     * @param int $id
     * @return Course
     */
    public function findById(int $id)
    {
        return $this->newQuery()->findOrFail($id);
    }

    /**
     * Retrieves a course by its unique course code.
     * Throws an exception if the course does not exist.
     *
     * @param string $code
     * @return Course
     */
    public function findByCode(string $code)
    {
        return $this->newQuery()
            ->where('code', $code)
            ->firstOrFail();
    }

    /**
     * Retrieves all courses belonging to a specific department.
     *
     * @param string $department
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findByDepartment(string $department)
    {
        return $this->newQuery()
            ->where('department', $department)
            ->get();
    }

    /**
     * Retrieves all courses that are currently marked as active.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findActive()
    {
        return $this->newQuery()
            ->where('status', 'active')
            ->get();
    }

    /**
     * Find all courses that require a specific book.
     *
     * @param int $bookId
     * @return \Illuminate\Support\Collection
     */
    public function findByBookId(int $bookId)
    {
        return $this->newQuery()
            ->whereHas('books', function ($query) use ($bookId) {
                $query->where('books.id', $bookId);
            })
            ->with(['books' => function ($query) use ($bookId) {
                $query->where('books.id', $bookId);
            }])
            ->get();
    }
}
