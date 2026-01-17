<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\JsonResponse;

class AttendanceController extends Controller
{
    /**
     * Get all students attending a specific class.
     *
     * @param int $classId
     * @return JsonResponse
     */
    public function studentsByClass(int $classId): JsonResponse
    {
        $attendance = new Attendance();

        $students = $attendance->getStudentsByClass($classId);

        return response()->json([
            'data' => $students,
        ]);
    }

    /**
     * Get all classes attended by a specific student.
     *
     * @param int $studentId
     * @return JsonResponse
     */
    public function classesByStudent(int $studentId): JsonResponse
    {
        $attendance = new Attendance();

        $classes = $attendance->getClassesByStudent($studentId);

        return response()->json([
            'data' => $classes,
        ]);
    }
}
