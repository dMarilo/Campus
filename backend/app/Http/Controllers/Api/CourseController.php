<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * Course model instance.
     * Used to manage course-related data and operations.
     */
    protected Course $course;

    /**
     * Injects the Course model into the controller.
     *
     * @param Course $course
     */
    public function __construct(Course $course)
    {
        $this->course = $course;
    }

    // READ

    /**
     * Retrieves all courses.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return response()->json(
            $this->course->findAll()
        );
    }

    /**
     * Retrieves a course by its unique identifier.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function showById(int $id)
    {
        return response()->json(
            $this->course->findById($id)
        );
    }

    /**
     * Retrieves a course by its unique course code.
     *
     * @param string $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function showByCode(string $code)
    {
        return response()->json(
            $this->course->findByCode($code)
        );
    }

    /**
     * Retrieves all courses belonging to a specific department.
     *
     * @param string $department
     * @return \Illuminate\Http\JsonResponse
     */
    public function showByDepartment(string $department)
    {
        return response()->json(
            $this->course->findByDepartment($department)
        );
    }

    /**
     * Retrieves all courses that are currently marked as active.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function active()
    {
        return response()->json(
            $this->course->findActive()
        );
    }

    // WRITE

    /**
     * Creates a new course record.
     *
     * The request data is filtered using the model's fillable attributes
     * to ensure safe mass assignment.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $course = $this->course->create(
            $request->only($this->course->getFillable())
        );

        return response()->json($course, 201);
    }

    /**
     * Updates an existing course record.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $id)
    {
        $course = $this->course->findById($id);
        $course->update(
            $request->only($this->course->getFillable())
        );

        return response()->json($course);
    }

    /**
     * Deletes a course record from the database.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id)
    {
        $course = $this->course->findById($id);
        $course->delete();

        return response()->json([
            'message' => 'Course deleted successfully'
        ]);
    }
}
