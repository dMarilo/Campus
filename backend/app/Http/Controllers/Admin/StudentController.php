<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;

class StudentController extends Controller
{
    public function update(Request $request, int $id)
    {
        $validated = $request->validate([
            'email'         => ['nullable', 'email'],
            'first_name'    => ['nullable', 'string', 'max:100'],
            'last_name'     => ['nullable', 'string', 'max:100'],
            'phone'         => ['nullable', 'string', 'max:30'],
            'date_of_birth' => ['nullable', 'date'],
            'gender'        => ['nullable', 'string', 'in:male,female,other'],
            'student_index' => ['nullable', 'string', 'max:50'],
            'code'          => ['nullable', 'string', 'max:50'],
            'year_of_study' => ['nullable', 'integer', 'min:1', 'max:10'],
            'department'    => ['nullable', 'string', 'max:100'],
            'gpa'           => ['nullable', 'numeric', 'min:0', 'max:10'],
            'status'        => [
                'nullable',
                Rule::in([
                    Student::STATUS_ACTIVE,
                    Student::STATUS_GRADUATED,
                    Student::STATUS_SUSPENDED,
                ]),
            ],
        ]);

        $student = Student::updateByAdmin($id, $validated);

        return response()->json([
            'data' => $student,
        ]);
    }
        /**
     * Get all students
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $students = Student::getAllStudents();

            return response()->json([
                'success' => true,
                'data' => $students,
                'count' => $students->count()
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve students',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a student by ID
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $student = Student::getStudentById($id);

            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $student
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve student',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a student by code
     *
     * @param string $code
     * @return JsonResponse
     */
    public function showByCode(string $code): JsonResponse
    {
        try {
            $student = Student::getStudentByCode($code);

            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $student
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve student',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all students from a specific year of study
     *
     * @param int $year
     * @return JsonResponse
     */
    public function showByYear(int $year): JsonResponse
    {
        try {
            $students = Student::getStudentsByYearOfStudy($year);

            return response()->json([
                'success' => true,
                'data' => $students,
                'count' => $students->count(),
                'year' => $year
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve students',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
