<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Book;
use App\Models\BookCopy;

class BookCopySeeder extends Seeder
{
    public function run(): void
    {
        $books = Book::all();

        foreach ($books as $book) {

            for ($i = 1; $i <= $book->total_copies; $i++) {

                // Unique physical identifier for each copy
                $isbn = str_pad($book->id, 5, '0', STR_PAD_LEFT)
                      . str_pad($i, 2, '0', STR_PAD_LEFT)
                      . rand(100, 999);

                BookCopy::create([
                    'book_id' => $book->id,
                    'isbn'    => $isbn,
                    'status'  => 'available',
                ]);
            }
        }
    }
}
