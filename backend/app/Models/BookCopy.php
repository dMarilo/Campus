<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookCopy extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | Constants
    |--------------------------------------------------------------------------
    */

    /**
     * Possible statuses for a book copy.
     */
    public const STATUS_AVAILABLE = 'available';
    public const STATUS_BORROWED  = 'borrowed';
    public const STATUS_DAMAGED   = 'damaged';

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    /**
     * The attributes that are mass assignable.
     *
     * These fields represent a single physical copy
     * of a book in the library inventory.
     */
    protected $fillable = [
        'book_id',
        'isbn',
        'status',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * The book this copy belongs to.
     *
     * Multiple copies can exist for a single book.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * All borrowings associated with this book copy.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function borrowings()
    {
        return $this->hasMany(Borrowing::class);
    }

    /**
     * The current active borrowing of this copy, if any.
     *
     * A copy can only have one active borrowing at a time,
     * identified by a NULL returned_at timestamp.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function currentBorrowing()
    {
        return $this->hasOne(Borrowing::class)
            ->whereNull('returned_at');
    }

    /*
    |--------------------------------------------------------------------------
    | Domain Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Determine whether the book copy is available for borrowing.
     *
     * A copy is considered available if:
     * - its status is "available"
     * - it has no active borrowing
     *
     * @return bool
     */
    public function isAvailable(): bool
    {
        return $this->status === self::STATUS_AVAILABLE
            && $this->currentBorrowing === null;
    }

    /**
     * Mark the book copy as borrowed.
     *
     * This does not create a borrowing record;
     * it only updates the copy state.
     *
     * @return bool
     */
    public function markAsBorrowed(): bool
    {
        return $this->update([
            'status' => self::STATUS_BORROWED,
        ]);
    }

    /**
     * Mark the book copy as available.
     *
     * Typically used when a book is returned.
     *
     * @return bool
     */
    public function markAsAvailable(): bool
    {
        return $this->update([
            'status' => self::STATUS_AVAILABLE,
        ]);
    }

    /**
     * Mark the book copy as damaged.
     *
     * Damaged copies should not be borrowable.
     *
     * @return bool
     */
    public function markAsDamaged(): bool
    {
        return $this->update([
            'status' => self::STATUS_DAMAGED,
        ]);
    }
}
