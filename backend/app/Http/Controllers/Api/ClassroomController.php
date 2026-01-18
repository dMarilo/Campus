<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ClassroomController extends Controller
{
    /**
     * Display a list of classrooms
     */
    public function index(): JsonResponse
    {
        $classrooms = Classroom::query()
            ->allClassrooms()
            ->get();

        return response()->json([
            'data' => $classrooms,
        ]);
    }




    /**
     * Start a classroom session (professor action)
     */
    public function startSession(Request $request, int $classroomId): JsonResponse
    {
        $request->validate([
            'class_pin'       => ['required', 'string'],
            'professor_code'  => ['required', 'string'],
        ]);

        $classroom = Classroom::findOrFail($classroomId);

        try {
            $session = $classroom->startSession(
                $request->input('class_pin'),
                $request->input('professor_code')
            );

            return response()->json([
                'message' => 'Session started successfully.',
                'data'    => $session,
            ], 201);

        } catch (ValidationException $e) {
            // Domain validation errors (PIN, professor, availability, etc.)
            throw $e;
        }
    }
}
