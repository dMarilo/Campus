<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Teaching;
use Illuminate\Http\JsonResponse;

class TeachingController extends Controller
{
    /**
     * Get all professors teaching a specific class.
     *
     * @param int $classId
     * @return JsonResponse
     */
    public function professorsByClass(int $classId): JsonResponse
    {
        $teaching = new Teaching();

        $professors = $teaching->getProfessorsByClass($classId);

        return response()->json([
            'data' => $professors,
        ]);
    }

    /**
     * Get all classes taught by a specific professor.
     *
     * @param int $professorId
     * @return JsonResponse
     */
    public function classesByProfessor(int $professorId): JsonResponse
    {
        $teaching = new Teaching();

        $classes = $teaching->getClassesByProfessor($professorId);

        return response()->json([
            'data' => $classes,
        ]);
    }
}
