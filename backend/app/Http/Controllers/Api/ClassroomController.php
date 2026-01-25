<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\ClassroomSession;
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

            $session->load([
                'classroom',
                'professor',
                'courseClass.course',
            ]);

            return response()->json([
                'message' => 'Session started successfully.',
                'data'    => $session,
            ], 201);

        } catch (ValidationException $e) {
            // Domain validation errors (PIN, professor, availability, etc.)
            throw $e;
        }
    }

    public function endSession(int $classroomId): JsonResponse
    {
        $classroom = Classroom::findOrFail($classroomId);

        $session = $classroom->endSession();

        return response()->json([
            'message' => 'Session ended successfully.',
            'data'    => $session,
        ]);
    }

    public function studentCheckIn(Request $request, int $classroomId)
    {
        $request->validate([
            'student_code' => ['required', 'string'],
        ]);

        // Resolve ongoing session for classroom
        $session = ClassroomSession::where('classroom_id', $classroomId)
            ->where('status', 'ongoing')
            ->firstOrFail();

        $status = $session->checkInStudentByCode(
            $request->input('student_code')
        );

        return response()->json([
            'message' => 'Attendance recorded.',
            'status'  => $status,
        ], 201);
    }
}
