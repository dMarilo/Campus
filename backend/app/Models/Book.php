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

    public function copies()
    {
        return $this->hasMany(BookCopy::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Domain Methods
    |--------------------------------------------------------------------------
    */

    public function getAllBooks()
    {
        return self::query()->get();
    }

    public function getBookById(int $id): Book
    {
        return self::query()->findOrFail($id);
    }

    public function search(string $query)
    {
        $keywords = preg_split('/\s+/', trim($query));

        return self::query()
            ->where(function ($q) use ($keywords) {
                foreach ($keywords as $word) {
                    $q->where('title', 'LIKE', "%{$word}%");
                }
            })
            ->get();
    }

    public function createBook(array $data): Book
    {
        return self::create($data);
    }

    public function updateBook(int $id, array $data): Book
    {
        $book = $this->getBookById($id);
        $book->update($data);

        return $book;
    }

    public function deleteBook(int $id): bool
    {
        $book = $this->getBookById($id);
        return (bool) $book->delete();
    }
}
