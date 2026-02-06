<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Professor;

class ProfessorController extends Controller
{
    /**
     * Retrieves all professors.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $professors = (new Professor)->getAllProfessors();

        return response()->json([
            'success' => true,
            'data' => $professors
        ]);
    }

    /**
     * Retrieves a professor by ID.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $professor = (new Professor)->getProfessorById($id);

        return response()->json([
            'success' => true,
            'data' => $professor
        ]);
    }

    /**
     * Retrieves a professor by their unique code.
     *
     * @param string $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function showByCode($code)
    {
        $professor = (new Professor)->getProfessorByCode($code);

        return response()->json([
            'success' => true,
            'data' => $professor
        ]);
    }

    /**
     * Searches professors by name.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $request->validate([
            'name' => 'required|string|min:1',
        ]);

        $searchTerm = $request->query('name');
        $professors = (new Professor)->searchProfessorsByName($searchTerm);

        return response()->json([
            'success' => true,
            'data' => $professors
        ]);
    }

    /**
     * Retrieves professors by department.
     *
     * @param string $department
     * @return \Illuminate\Http\JsonResponse
     */
    public function showByDepartment($department)
    {
        $professors = (new Professor)->getProfessorsByDepartment($department);

        return response()->json([
            'success' => true,
            'data' => $professors
        ]);
    }

    /**
     * Retrieves only active professors.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function active()
    {
        $professors = (new Professor)->getActiveProfessors();

        return response()->json([
            'success' => true,
            'data' => $professors
        ]);
    }

    /**
     * Creates and stores a new professor.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id'         => 'nullable|integer|exists:users,id',
            'code'            => 'required|string|unique:professors,code',
            'first_name'      => 'required|string',
            'last_name'       => 'required|string',
            'email'           => 'required|email|unique:professors,email',
            'phone'           => 'nullable|string',
            'academic_title'  => 'nullable|string',
            'department'      => 'required|string',
            'employment_type' => 'required|string|in:full_time,part_time,external',
            'status'          => 'nullable|string|in:active,inactive',
            'office_location' => 'nullable|string',
            'office_hours'    => 'nullable|string',
        ]);

        $professor = (new Professor)->loadProfessor($validated);

        return response()->json([
            'success' => true,
            'message' => 'Professor successfully added.',
            'data' => $professor
        ], 201);
    }

    /**
     * Updates an existing professor.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'user_id'         => 'nullable|integer|exists:users,id',
            'code'            => 'nullable|string|unique:professors,code,' . $id,
            'first_name'      => 'nullable|string',
            'last_name'       => 'nullable|string',
            'email'           => 'nullable|email|unique:professors,email,' . $id,
            'phone'           => 'nullable|string',
            'academic_title'  => 'nullable|string',
            'department'      => 'nullable|string',
            'employment_type' => 'nullable|string|in:full_time,part_time,external',
            'status'          => 'nullable|string|in:active,inactive',
            'office_location' => 'nullable|string',
            'office_hours'    => 'nullable|string',
        ]);

        $professor = (new Professor)->updateProfessor($id, $validated);

        return response()->json([
            'success' => true,
            'message' => 'Professor updated successfully.',
            'data' => $professor
        ]);
    }

    /**
     * Deletes a professor.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        (new Professor)->deleteProfessor($id);

        return response()->json([
            'success' => true,
            'message' => 'Professor deleted successfully.'
        ]);
    }
}
