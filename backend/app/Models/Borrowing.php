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

    public const STATUS_BORROWED = 'borrowed';
    public const STATUS_RETURNED = 'returned';

    protected $fillable = [
        'book_copy_id',
        'student_id',
        'borrowed_at',
        'due_at',
        'returned_at',
        'status',
    ];

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

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function bookCopy()
    {
        return $this->belongsTo(BookCopy::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Domain Logic
    |--------------------------------------------------------------------------
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

            // Update copy
            $copy->status = BookCopy::STATUS_BORROWED;
            $copy->save();

            // Update book
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

            // Update copy
            $copy->status = BookCopy::STATUS_AVAILABLE;
            $copy->save();

            // Update book (guarded)
            if ($book->available_copies < $book->total_copies) {
                $book->available_copies += 1;
                $book->save();
            }

            // Update borrowing
            $borrowing->status = self::STATUS_RETURNED;
            $borrowing->returned_at = now();
            $borrowing->save();

            return $borrowing;
        });
    }


    public function historyForStudent(int $studentId)
    {
        return self::with(['bookCopy.book'])
            ->where('student_id', $studentId)
            ->orderByDesc('borrowed_at')
            ->get();
    }

    public function currentForStudent(int $studentId)
    {
        return self::with(['bookCopy.book'])
            ->where('student_id', $studentId)
            ->whereNull('returned_at')
            ->orderByDesc('borrowed_at')
            ->get();
    }

    public function allActive()
    {
        return self::with(['student', 'bookCopy.book'])
            ->whereNull('returned_at')
            ->orderByDesc('borrowed_at')
            ->get();
    }
}
