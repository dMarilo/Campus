<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ExamResult;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExamResultController extends Controller
{
    /**
     * Get all exam results for a specific student.
     *
     * @param int $studentId
     * @return JsonResponse
     */
    public function studentResults(int $studentId): JsonResponse
    {
        $student = Student::find($studentId);

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found',
            ], 404);
        }

        $results = ExamResult::getResultsForStudent($studentId);

        $formattedResults = $results->map(function ($result) {
            return [
                'id' => $result->id,
                'exam_id' => $result->exam_id,
                'exam_title' => $result->exam->title,
                'exam_type' => $result->exam->type,
                'class_name' => $result->exam->courseClass->name ?? null,
                'grade' => $result->grade,
                'max_points' => $result->exam->max_points,
                'percentage' => $result->getPercentage(),
                'letter_grade' => $result->getLetterGrade(),
                'passed' => $result->passed,
                'registration_date' => $result->registration_date,
                'exam_date' => $result->exam->exam_date,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $formattedResults,
        ]);
    }

    /**
     * Get passed exam results for a student.
     *
     * @param int $studentId
     * @return JsonResponse
     */
    public function passedResults(int $studentId): JsonResponse
    {
        $student = Student::find($studentId);

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found',
            ], 404);
        }

        $results = ExamResult::getPassedResultsForStudent($studentId);

        return response()->json([
            'success' => true,
            'data' => $results,
        ]);
    }

    /**
     * Get all exam results for a specific class.
     *
     * @param int $classId
     * @return JsonResponse
     */
    public function classResults(int $classId): JsonResponse
    {
        $results = ExamResult::getResultsForClass($classId);

        $formattedResults = $results->map(function ($result) {
            return [
                'id' => $result->id,
                'student_id' => $result->student_id,
                'student_name' => $result->student->user->name ?? null,
                'student_code' => $result->student->student_code ?? null,
                'exam_title' => $result->exam->title,
                'exam_type' => $result->exam->type,
                'grade' => $result->grade,
                'max_points' => $result->exam->max_points,
                'percentage' => $result->getPercentage(),
                'letter_grade' => $result->getLetterGrade(),
                'passed' => $result->passed,
                'registration_date' => $result->registration_date,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $formattedResults,
        ]);
    }

    /**
     * Get a single exam result.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $result = ExamResult::with(['exam', 'student'])->find($id);

        if (!$result) {
            return response()->json([
                'success' => false,
                'message' => 'Exam result not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $result->id,
                'exam' => [
                    'id' => $result->exam->id,
                    'title' => $result->exam->title,
                    'type' => $result->exam->type,
                    'max_points' => $result->exam->max_points,
                    'exam_date' => $result->exam->exam_date,
                ],
                'student' => [
                    'id' => $result->student->id,
                    'name' => $result->student->user->name ?? null,
                    'student_code' => $result->student->student_code ?? null,
                ],
                'grade' => $result->grade,
                'percentage' => $result->getPercentage(),
                'letter_grade' => $result->getLetterGrade(),
                'passed' => $result->passed,
                'registration_date' => $result->registration_date,
            ],
        ]);
    }

    /**
     * Delete an exam result.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $result = ExamResult::find($id);

        if (!$result) {
            return response()->json([
                'success' => false,
                'message' => 'Exam result not found',
            ], 404);
        }

        $result->delete();

        return response()->json([
            'success' => true,
            'message' => 'Exam result deleted successfully',
        ]);
    }
}
