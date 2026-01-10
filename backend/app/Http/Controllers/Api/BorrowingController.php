<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BorrowingController extends Controller
{
    /**
     * Handles the borrowing of a book copy by a student.
     *
     * This endpoint:
     *  - Validates the student and book copy identifiers
     *  - Delegates borrowing logic to the Borrowing domain model
     *  - Creates a borrowing record and sets its status to borrowed
     *  - Updates the book copy status and book availability count
     *
     * If borrowing rules are violated (e.g. copy not available),
     * an error response is returned.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function borrow(Request $request)
    {
        $validated = $request->validate([
            'student_id'   => ['required', 'integer', 'exists:students,id'],
            'book_copy_id' => ['required', 'integer', 'exists:book_copies,id'],
        ]);

        try {
            $borrowing = (new Borrowing)->borrow(
                $validated['student_id'],
                $validated['book_copy_id']
            );

            return response()->json([
                'message' => 'Book successfully borrowed.',
                'data' => $borrowing,
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Handles the return of a previously borrowed book copy.
     *
     * This endpoint:
     *  - Validates the student and book copy identifiers
     *  - Finds the active borrowing record
     *  - Marks the borrowing as returned
     *  - Updates the book copy status back to available
     *  - Restores the available copies count of the related book
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function return(Request $request)
    {
        $validated = $request->validate([
            'student_id'   => ['required', 'integer', 'exists:students,id'],
            'book_copy_id' => ['required', 'integer', 'exists:book_copies,id'],
        ]);

        try {
            $borrowing = (new Borrowing)->return(
                $validated['student_id'],
                $validated['book_copy_id']
            );

            return response()->json([
                'message' => 'Book successfully returned.',
                'data' => $borrowing,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Retrieves borrowing information for a specific student.
     *
     * Depending on the provided type parameter, this endpoint returns:
     *  - The complete borrowing history of the student
     *  - Or only currently active (not yet returned) borrowings
     *
     * Results are ordered by borrowing date, newest first.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function studentBorrowings(Request $request)
    {
        $validated = $request->validate([
            'student_id' => ['required', 'integer', 'exists:students,id'],
            'type' => ['nullable', 'in:all,current'],
        ]);

        $borrowing = new Borrowing;

        if (($validated['type'] ?? 'all') === 'current') {
            $data = $borrowing->currentForStudent($validated['student_id']);
        } else {
            $data = $borrowing->historyForStudent($validated['student_id']);
        }

        return response()->json([
            'data' => $data,
        ]);
    }

    /**
     * Retrieves all currently active borrowings in the system.
     *
     * This endpoint is intended for administrative or librarian use.
     * It returns all borrowings that have not yet been returned,
     * including associated student and book information.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function allBorrowed()
    {
        $borrowings = (new Borrowing)->allActive();

        return response()->json([
            'data' => $borrowings,
        ]);
    }
}
