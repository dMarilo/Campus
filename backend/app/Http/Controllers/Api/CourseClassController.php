<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CourseClass;
use App\Models\Teaching;
use App\Models\Attendance;
use Illuminate\Http\JsonResponse;

class CourseClassController extends Controller
{
    /**
     * Display a listing of all classes.
     */
    public function index(): JsonResponse
    {
        $classes = CourseClass::query()
            ->with([
                'course',
                'semester',
                'academicYear',
            ])
            ->get();

        return response()->json([
            'data' => $classes,
        ]);
    }

    /**
     * Display a single class by ID with all relationships.
     */
    public function show(int $id): JsonResponse
    {
        $class = CourseClass::query()
            ->with([
                'course',
                'semester',
                'academicYear',
            ])
            ->findOrFail($id);

        return response()->json([
            'data' => $class,
        ]);
    }
}
