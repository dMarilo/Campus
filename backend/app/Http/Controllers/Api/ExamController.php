<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ExamController extends Controller
{
    /**
     * Get all exams.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $exams = Exam::with('courseClass.course')->get();

        return response()->json([
            'success' => true,
            'data' => $exams,
        ]);
    }

    /**
     * Get a single exam by ID.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $exam = Exam::with(['courseClass.course', 'examResults.student.user'])->find($id);

        if (!$exam) {
            return response()->json([
                'success' => false,
                'message' => 'Exam not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $exam,
        ]);
    }

    /**
     * Get all exams for a specific class.
     *
     * @param int $classId
     * @return JsonResponse
     */
    public function examsByClass(int $classId): JsonResponse
    {
        $exams = Exam::getExamScheduleByClass($classId);

        return response()->json([
            'success' => true,
            'data' => $exams,
        ]);
    }

    /**
     * Get all exam dates for a specific class.
     *
     * @param int $classId
     * @return JsonResponse
     */
    public function examDatesByClass(int $classId): JsonResponse
    {
        $exams = Exam::getExamScheduleByClass($classId);

        $schedule = $exams->map(function ($exam) {
            return [
                'id' => $exam->id,
                'type' => $exam->type,
                'title' => $exam->title,
                'exam_date' => $exam->exam_date,
                'exam_time' => $exam->exam_time,
                'classroom_name' => $exam->classroom_name,
                'status' => $exam->status,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $schedule,
        ]);
    }

    /**
     * Get all exams for a specific student.
     *
     * Includes exam results if the student has taken the exam.
     *
     * @param int $studentId
     * @return JsonResponse
     */
    public function examsByStudent(int $studentId): JsonResponse
    {
        $student = Student::find($studentId);

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found',
            ], 404);
        }

        $exams = Exam::getExamsForStudent($studentId);

        // Add additional info for each exam
        $examsWithStatus = $exams->map(function ($exam) use ($studentId) {
            $result = $exam->examResults->first();

            return [
                'id' => $exam->id,
                'class_id' => $exam->class_id,
                'class_name' => $exam->courseClass->name ?? null,
                'type' => $exam->type,
                'title' => $exam->title,
                'exam_date' => $exam->exam_date,
                'exam_time' => $exam->exam_time,
                'classroom_name' => $exam->classroom_name,
                'max_points' => $exam->max_points,
                'status' => $exam->status,
                'has_taken' => $result !== null,
                'result' => $result ? [
                    'grade' => $result->grade,
                    'passed' => $result->passed,
                    'registration_date' => $result->registration_date,
                    'letter_grade' => $result->getLetterGrade(),
                    'percentage' => $result->getPercentage(),
                ] : null,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $examsWithStatus,
        ]);
    }

    /**
     * Get upcoming exams for a specific student.
     *
     * @param int $studentId
     * @return JsonResponse
     */
    public function upcomingExamsByStudent(int $studentId): JsonResponse
    {
        $student = Student::find($studentId);

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found',
            ], 404);
        }

        $exams = Exam::whereHas('courseClass.attendances', function ($query) use ($studentId) {
                $query->where('student_id', $studentId);
            })
            ->upcoming()
            ->with('courseClass.course')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $exams,
        ]);
    }

    /**
     * Create a new exam.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'class_id' => 'required|exists:course_classes,id',
            'type' => 'required|in:' . implode(',', [
                Exam::TYPE_MIDTERM,
                Exam::TYPE_FINAL,
                Exam::TYPE_RETAKE,
                Exam::TYPE_QUIZ,
                Exam::TYPE_ORAL,
                Exam::TYPE_LAB_EXAM,
            ]),
            'title' => 'nullable|string|max:255',
            'exam_date' => 'required|date',
            'exam_time' => 'required|date_format:H:i',
            'classroom_name' => 'nullable|string|max:255',
            'max_points' => 'required|integer|min:1',
            'status' => 'nullable|in:' . implode(',', [
                Exam::STATUS_PLANNED,
                Exam::STATUS_OPEN,
                Exam::STATUS_CLOSED,
                Exam::STATUS_GRADED,
                Exam::STATUS_CANCELED,
            ]),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $exam = Exam::create($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Exam created successfully',
            'data' => $exam->load('courseClass.course'),
        ], 201);
    }

    /**
     * Update an existing exam.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $exam = Exam::find($id);

        if (!$exam) {
            return response()->json([
                'success' => false,
                'message' => 'Exam not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'class_id' => 'sometimes|exists:course_classes,id',
            'type' => 'sometimes|in:' . implode(',', [
                Exam::TYPE_MIDTERM,
                Exam::TYPE_FINAL,
                Exam::TYPE_RETAKE,
                Exam::TYPE_QUIZ,
                Exam::TYPE_ORAL,
                Exam::TYPE_LAB_EXAM,
            ]),
            'title' => 'nullable|string|max:255',
            'exam_date' => 'sometimes|date',
            'exam_time' => 'sometimes|date_format:H:i',
            'classroom_name' => 'nullable|string|max:255',
            'max_points' => 'sometimes|integer|min:1',
            'status' => 'sometimes|in:' . implode(',', [
                Exam::STATUS_PLANNED,
                Exam::STATUS_OPEN,
                Exam::STATUS_CLOSED,
                Exam::STATUS_GRADED,
                Exam::STATUS_CANCELED,
            ]),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $exam->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Exam updated successfully',
            'data' => $exam->load('courseClass.course'),
        ]);
    }

    /**
     * Delete an exam.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $exam = Exam::find($id);

        if (!$exam) {
            return response()->json([
                'success' => false,
                'message' => 'Exam not found',
            ], 404);
        }

        // Check if exam has results
        if ($exam->examResults()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete exam with existing results',
            ], 422);
        }

        $exam->delete();

        return response()->json([
            'success' => true,
            'message' => 'Exam deleted successfully',
        ]);
    }

    /**
     * Register a student for an exam (create exam result).
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function registerStudent(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'exam_id' => 'required|exists:exams,id',
            'student_id' => 'required|exists:students,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $exam = Exam::find($request->exam_id);

        // Check if student already registered
        if ($exam->hasStudentTaken($request->student_id)) {
            return response()->json([
                'success' => false,
                'message' => 'Student has already taken this exam',
            ], 422);
        }

        // Check if exam can be taken
        if (!$exam->canBeTaken()) {
            return response()->json([
                'success' => false,
                'message' => 'This exam is not available for registration',
            ], 422);
        }

        $examResult = ExamResult::create([
            'exam_id' => $request->exam_id,
            'student_id' => $request->student_id,
            'grade' => null,
            'passed' => false,
            'registration_date' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Student registered for exam successfully',
            'data' => $examResult->load(['exam', 'student']),
        ], 201);
    }

    /**
     * Submit/grade an exam result.
     *
     * @param Request $request
     * @param int $resultId
     * @return JsonResponse
     */
    public function gradeExam(Request $request, int $resultId): JsonResponse
    {
        $examResult = ExamResult::find($resultId);

        if (!$examResult) {
            return response()->json([
                'success' => false,
                'message' => 'Exam result not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'grade' => 'required|integer|min:0|max:' . $examResult->exam->max_points,
            'passed' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $examResult->registerGrade(
            $request->grade,
            $request->passed
        );

        return response()->json([
            'success' => true,
            'message' => 'Exam graded successfully',
            'data' => $examResult->load(['exam', 'student']),
        ]);
    }

    /**
     * Get exam statistics.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function statistics(int $id): JsonResponse
    {
        $exam = Exam::find($id);

        if (!$exam) {
            return response()->json([
                'success' => false,
                'message' => 'Exam not found',
            ], 404);
        }

        $totalStudents = $exam->examResults()->count();
        $passedStudents = $exam->examResults()->where('passed', true)->count();
        $failedStudents = $exam->examResults()->where('passed', false)->count();
        $gradedStudents = $exam->examResults()->whereNotNull('grade')->count();
        $ungradedStudents = $exam->examResults()->whereNull('grade')->count();

        return response()->json([
            'success' => true,
            'data' => [
                'exam_id' => $exam->id,
                'exam_title' => $exam->title,
                'total_students' => $totalStudents,
                'graded_students' => $gradedStudents,
                'ungraded_students' => $ungradedStudents,
                'passed_students' => $passedStudents,
                'failed_students' => $failedStudents,
                'pass_rate' => $exam->getPassRate(),
                'average_grade' => $exam->getAverageGrade(),
            ],
        ]);
    }

    /**
     * Change exam status.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function changeStatus(Request $request, int $id): JsonResponse
    {
        $exam = Exam::find($id);

        if (!$exam) {
            return response()->json([
                'success' => false,
                'message' => 'Exam not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:' . implode(',', [
                Exam::STATUS_PLANNED,
                Exam::STATUS_OPEN,
                Exam::STATUS_CLOSED,
                Exam::STATUS_GRADED,
                Exam::STATUS_CANCELED,
            ]),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $exam->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Exam status updated successfully',
            'data' => $exam,
        ]);
    }
}
