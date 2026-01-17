<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use Illuminate\Http\JsonResponse;

class ExamController extends Controller
{
    /**
     * Get all exam dates for a specific class.
     *
     * @param int $classId
     * @return JsonResponse
     */
    public function examDatesByClass(int $classId): JsonResponse
    {
        $exam = new Exam();


        $schedule = $exam->getExamScheduleByClass($classId);

        return response()->json([
            'data' => $schedule,
        ]);
    }
}
