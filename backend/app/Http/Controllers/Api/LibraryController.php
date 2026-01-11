<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LibraryController extends Controller
{
    /**
     * Retrieves a list of all books in the library.
     *
     * This endpoint:
     *  - Fetches all book records from the database
     *  - Returns bibliographic and inventory information for each book
     *
     * Access to this endpoint requires authentication.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $books = (new Book)->getAllBooks();

        return response()->json([
            'data' => $books
        ]);
    }

    /**
     * Retrieves a single book by its unique identifier.
     *
     * This endpoint:
     *  - Accepts a book ID as a route parameter
     *  - Returns the corresponding book record if it exists
     *  - Throws an error if the book cannot be found
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id)
    {
        $book = (new Book)->getBookById($id);

        return response()->json([
            'data' => $book
        ]);
    }

    /**
     * Searches for books using a free-text query.
     *
     * This endpoint:
     *  - Accepts a search query via request parameters
     *  - Performs partial matching against book titles
     *  - Returns all books that match the provided query
     *
     * The search is intended for user-facing discovery features.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * Creates a new book entry in the library.
     *
     * This endpoint:
     *  - Validates bibliographic and inventory data
     *  - Delegates persistence logic to the Book model
     *  - Initializes total and available copy counts
     *
     * Inventory values provided here are later managed automatically
     * through borrowing and returning operations.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * Updates an existing book entry.
     *
     * This endpoint:
     *  - Accepts partial or full book data updates
     *  - Applies changes only to the provided fields
     *  - Delegates update logic to the Book model
     *
     * This allows safe modification of book metadata
     * without overwriting unchanged attributes.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * Deletes a book from the library.
     *
     * This endpoint:
     *  - Removes the book record identified by the given ID
     *  - Permanently deletes the book from the system
     *
     * This operation should be used with caution, as it
     * removes all bibliographic data for the book.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id)
    {
        (new Book)->deleteBook($id);

        return response()->json([
            'message' => 'Book deleted successfully.'
        ]);
    }

    public function byCourse(int $courseId)
    {
        $book = new \App\Models\Book();

        return response()->json([
            'data' => $book->findByCourseId($courseId),
        ]);
    }
}
