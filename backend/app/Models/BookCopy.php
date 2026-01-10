<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookCopy extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_id',
        'isbn',
        'status',
    ];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function borrowings()
    {
        return $this->hasMany(Borrowing::class);
    }

    public function currentBorrowing()
    {
        return $this->hasOne(Borrowing::class)
            ->whereNull('returned_at');
    }

    public function isAvailable(): bool
    {
        return $this->status === 'available' && $this->currentBorrowing === null;
    }
}
