<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class Borrowing extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | Constants
    |--------------------------------------------------------------------------
    */

    /**
     * Indicates that a book copy is currently borrowed.
     */
    public const STATUS_BORROWED = 'borrowed';

    /**
     * Indicates that a book copy has been returned.
     */
    public const STATUS_RETURNED = 'returned';

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    /**
     * The attributes that are mass assignable.
     *
     * These fields represent a single borrowing transaction
     * between a student and a specific book copy.
     */
    protected $fillable = [
        'book_copy_id',
        'student_id',
        'borrowed_at',
        'due_at',
        'returned_at',
        'status',
    ];

    /*
    |--------------------------------------------------------------------------
    | Attribute Casting
    |--------------------------------------------------------------------------
    */

    /**
     * The attributes that should be cast to native types.
     *
     * Dates are automatically converted into Carbon instances.
     */
    protected $casts = [
        'borrowed_at' => 'datetime',
        'due_at' => 'datetime',
        'returned_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Defines the relationship between a borrowing and a student.
     *
     * Each borrowing belongs to exactly one student.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Defines the relationship between a borrowing and a book copy.
     *
     * Each borrowing is associated with exactly one physical book copy.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function bookCopy()
    {
        return $this->belongsTo(BookCopy::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Domain Logic
    |--------------------------------------------------------------------------
    */

    /**
     * Borrows a book copy for a given student.
     *
     * This operation:
     *  - Locks the book copy row to prevent race conditions
     *  - Ensures the copy is available
     *  - Decreases the available copies count on the book
     *  - Marks the book copy as borrowed
     *  - Creates a borrowing record with a due date
     *
     * @param int $studentId
     * @param int $bookCopyId
     * @return Borrowing
     * @throws \Exception
     */
    public function borrow(int $studentId, int $bookCopyId): Borrowing
    {
        return DB::transaction(function () use ($studentId, $bookCopyId) {

            $copy = BookCopy::with('book')->lockForUpdate()->findOrFail($bookCopyId);

            if ($copy->status !== 'available') {
                throw new \Exception('Book copy is not available.');
            }

            $book = $copy->book;

            if ($book->available_copies <= 0) {
                throw new \Exception('No available copies of this book.');
            }

            // Mark copy as borrowed
            $copy->status = BookCopy::STATUS_BORROWED;
            $copy->save();

            // Decrease available copies
            $book->available_copies -= 1;
            $book->save();

            return self::create([
                'student_id'    => $studentId,
                'book_copy_id'  => $bookCopyId,
                'borrowed_at'   => now(),
                'due_at'        => now()->addDays(30),
                'status'        => self::STATUS_BORROWED,
            ]);
        });
    }

    /**
     * Returns a previously borrowed book copy.
     *
     * This operation:
     *  - Finds the active borrowing record
     *  - Locks the borrowing row to prevent race conditions
     *  - Marks the book copy as available
     *  - Increases the available copies count on the book
     *  - Marks the borrowing as returned
     *
     * @param int $studentId
     * @param int $bookCopyId
     * @return Borrowing
     * @throws \Exception
     */
    public function return(int $studentId, int $bookCopyId): Borrowing
    {
        return DB::transaction(function () use ($studentId, $bookCopyId) {

            $borrowing = self::where('student_id', $studentId)
                ->where('book_copy_id', $bookCopyId)
                ->where('status', self::STATUS_BORROWED)
                ->lockForUpdate()
                ->firstOrFail();

            $copy = BookCopy::with('book')->findOrFail($bookCopyId);
            $book = $copy->book;

            // Mark copy as available
            $copy->status = BookCopy::STATUS_AVAILABLE;
            $copy->save();

            // Increase available copies (guarded)
            if ($book->available_copies < $book->total_copies) {
                $book->available_copies += 1;
                $book->save();
            }

            // Mark borrowing as returned
            $borrowing->status = self::STATUS_RETURNED;
            $borrowing->returned_at = now();
            $borrowing->save();

            return $borrowing;
        });
    }

    /**
     * Retrieves the full borrowing history for a specific student.
     *
     * Includes both returned and currently borrowed books,
     * ordered from newest to oldest.
     *
     * @param int $studentId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function historyForStudent(int $studentId)
    {
        return self::with(['bookCopy.book'])
            ->where('student_id', $studentId)
            ->orderByDesc('borrowed_at')
            ->get();
    }

    /**
     * Retrieves all currently borrowed books for a student.
     *
     * Only active borrowings (not yet returned) are included.
     *
     * @param int $studentId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function currentForStudent(int $studentId)
    {
        return self::with(['bookCopy.book'])
            ->where('student_id', $studentId)
            ->whereNull('returned_at')
            ->orderByDesc('borrowed_at')
            ->get();
    }

    /**
     * Retrieves all active borrowings in the system.
     *
     * Intended for administrative or librarian views,
     * including student and book information.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function allActive()
    {
        return self::with(['student', 'bookCopy.book'])
            ->whereNull('returned_at')
            ->orderByDesc('borrowed_at')
            ->get();
    }
}
