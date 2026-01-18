<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use Illuminate\Http\JsonResponse;

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
}
