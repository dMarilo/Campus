<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CourseClass;
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
}
