<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    /**
     * The attributes that are mass assignable.
     *
     * These fields represent the core bibliographic
     * and inventory-related data for a book.
     */
    protected $fillable = [
        'title',
        'author',
        'publisher',
        'published_year',
        'edition',
        'description',
        'total_copies',
        'available_copies',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Defines the relationship between a book and its physical copies.
     *
     * A single book can have multiple book copies,
     * each representing a physical экземпляр.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function copies()
    {
        return $this->hasMany(BookCopy::class);
    }

    public function courses()
    {
        return $this->belongsToMany(\App\Models\Course::class,'course_book')->withPivot('mandatory')
        ->withTimestamps();
    }


    /*
    |--------------------------------------------------------------------------
    | Domain Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Retrieves all books stored in the system.
     *
     * This method returns every book record,
     * regardless of availability or copy status.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllBooks()
    {
        return self::query()->get();
    }

    /**
     * Retrieves a single book by its unique identifier.
     *
     * Throws an exception if the book does not exist.
     *
     * @param int $id
     * @return Book
     */
    public function getBookById(int $id): Book
    {
        return self::query()->findOrFail($id);
    }

    /**
     * Searches for books by title using keyword-based matching.
     *
     * The provided query string is split into words,
     * and all words must appear in the book title.
     *
     * @param string $query
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function search(string $query)
    {
        $keywords = preg_split('/\s+/', trim($query));

        return self::query()
            ->where(function ($q) use ($keywords) {
                foreach ($keywords as $word) {
                    $q->where(function ($sub) use ($word) {
                        $sub->where('title', 'LIKE', "%{$word}%")
                            ->orWhere('author', 'LIKE', "%{$word}%")
                            ->orWhere('publisher', 'LIKE', "%{$word}%");
                    });
                }
            })
            ->get();
    }

    /**
     * Creates and persists a new book record.
     *
     * This method encapsulates book creation logic
     * and should be used by controllers instead
     * of calling create() directly.
     *
     * @param array $data
     * @return Book
     */
    public function createBook(array $data): Book
    {
        return self::create($data);
    }

    /**
     * Updates an existing book record.
     *
     * Retrieves the book by ID, applies the provided
     * changes, and returns the updated model.
     *
     * @param int $id
     * @param array $data
     * @return Book
     */
    public function updateBook(int $id, array $data): Book
    {
        $book = $this->getBookById($id);
        $book->update($data);

        return $book;
    }

    /**
     * Deletes a book from the system.
     *
     * This operation permanently removes the book record.
     * An exception is thrown if the book does not exist.
     *
     * @param int $id
     * @return bool
     */
    public function deleteBook(int $id): bool
    {
        $book = $this->getBookById($id);
        return (bool) $book->delete();
    }


    /**
     * Get all books for a given course.
     *
     * @param int $courseId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findByCourseId(int $courseId)
    {
        return $this->newQuery()
            ->whereHas('courses', function ($query) use ($courseId) {
                $query->where('courses.id', $courseId);
            })
            ->get();
    }
}
