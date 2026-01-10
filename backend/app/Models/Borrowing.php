<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Borrowing extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_copy_id',
        'student_id',
        'borrowed_at',
        'due_at',
        'returned_at',
    ];

    protected $casts = [
        'borrowed_at' => 'datetime',
        'due_at' => 'datetime',
        'returned_at' => 'datetime',
    ];

    public function bookCopy()
    {
        return $this->belongsTo(BookCopy::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function isReturned(): bool
    {
        return $this->returned_at !== null;
    }
}
