<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LibraryController extends Controller
{
    public function index()
    {
        $books = (new Book)->getAllBooks();

        return response()->json([
            'data' => $books
        ]);
    }

    public function show(int $id)
    {
        $book = (new Book)->getBookById($id);

        return response()->json([
            'data' => $book
        ]);
    }

    public function search(Request $request)
    {
        $validated = $request->validate([
            'q' => ['required', 'string', 'min:1'],
        ]);

        $books = (new Book)->search($validated['q']);

        return response()->json([
            'data' => $books
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'            => ['required', 'string'],
            'author'           => ['required', 'string'],
            'publisher'        => ['nullable', 'string'],
            'published_year'   => ['nullable', 'integer'],
            'edition'          => ['nullable', 'string'],
            'description'      => ['nullable', 'string'],
            'total_copies'     => ['required', 'integer', 'min:1'],
            'available_copies' => ['required', 'integer', 'min:0'],
        ]);

        $book = (new Book)->createBook($validated);

        return response()->json([
            'message' => 'Book created successfully.',
            'data' => $book
        ], Response::HTTP_CREATED);
    }

    public function update(Request $request, int $id)
    {
        $validated = $request->validate([
            'title'            => ['sometimes', 'string'],
            'author'           => ['sometimes', 'string'],
            'publisher'        => ['nullable', 'string'],
            'published_year'   => ['nullable', 'integer'],
            'edition'          => ['nullable', 'string'],
            'description'      => ['nullable', 'string'],
            'total_copies'     => ['sometimes', 'integer', 'min:1'],
            'available_copies' => ['sometimes', 'integer', 'min:0'],
        ]);

        $book = (new Book)->updateBook($id, $validated);

        return response()->json([
            'message' => 'Book updated successfully.',
            'data' => $book
        ]);
    }

    public function destroy(int $id)
    {
        (new Book)->deleteBook($id);

        return response()->json([
            'message' => 'Book deleted successfully.'
        ]);
    }
}
