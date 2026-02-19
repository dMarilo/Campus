<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClassroomSession;
use Illuminate\Http\JsonResponse;

class SessionController extends Controller
{
    /**
     * List all classroom sessions
     */
    public function index(): JsonResponse
    {
        $sessions = ClassroomSession::with([
            'classroom',
            'courseClass.course',
            'professor',
        ])
            ->orderByDesc('starts_at')
            ->get();

        return response()->json([
            'data' => $sessions,
        ]);
    }

    /**
     * Show a single session with student attendance
     */
    public function show(int $id): JsonResponse
    {
        $session = ClassroomSession::with([
            'classroom',
            'courseClass.course',
            'professor',
        ])->findOrFail($id);

        $sessionData = $session->toArray();
        $sessionData['students'] = $session->getStudentsWithAttendance();

        return response()->json([
            'data' => $sessionData,
        ]);
    }
}
