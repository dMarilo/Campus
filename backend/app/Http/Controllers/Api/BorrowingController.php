<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BorrowingController extends Controller
{
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

    public function allBorrowed()
    {
        $borrowings = (new Borrowing)->allActive();

        return response()->json([
            'data' => $borrowings,
        ]);
    }
}
