<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Dorm;

class DormController extends Controller
{
    /**
     * Retrieves all dormitories.
     *
     * This endpoint returns a list of all dorms stored in the system.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllDorms()
    {
        $dorms = (new Dorm)->getAllDorms();

        return response()->json([
            'success' => true,
            'data' => $dorms
        ]);
    }

    /**
     * Retrieves a dormitory by its unique identifier.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDormById($id)
    {
        $dorm = (new Dorm)->getDormById($id);

        return response()->json([
            'success' => true,
            'data' => $dorm
        ]);
    }

    /**
     * Searches dormitories by name.
     *
     * This endpoint supports partial name matching and is intended
     * for quick lookup and filtering.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchDorms(Request $request)
    {
        $request->validate([
            'name' => 'required|string|min:1',
        ]);

        $searchTerm = $request->query('name');

        $dorms = (new Dorm)->searchDormsByName($searchTerm);

        return response()->json([
            'success' => true,
            'data' => $dorms
        ]);
    }

    /**
     * Creates and stores a new dormitory.
     *
     * The request data is validated and passed to the Dorm model
     * for persistence.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function loadDorm(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'address' => 'nullable|string',
            'total_rooms' => 'required|integer|min:0',
            'total_beds' => 'required|integer|min:0',
            'description' => 'nullable|string',
        ]);

        $dorm = (new Dorm)->loadDorm($validated);

        return response()->json([
            'success' => true,
            'message' => 'Dorm successfully added.',
            'data' => $dorm
        ], 201);
    }

    /**
     * Updates an existing dormitory.
     *
     * Only the provided fields are updated.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateDorm(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'nullable|string',
            'address' => 'nullable|string',
            'total_rooms' => 'nullable|integer|min:0',
            'total_beds' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
        ]);

        $dorm = (new Dorm)->updateDorm($id, $validated);

        return response()->json([
            'success' => true,
            'message' => 'Dorm updated successfully.',
            'data' => $dorm
        ]);
    }

    /**
     * Deletes a dormitory.
     *
     * This operation permanently removes the dormitory record.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteDorm($id)
    {
        (new Dorm)->deleteDorm($id);

        return response()->json([
            'success' => true,
            'message' => 'Dorm deleted successfully.'
        ]);
    }

    /**
     * Retrieves the total bed capacity of a dormitory.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDormCapacity($id)
    {
        $capacity = (new Dorm)->getDormCapacity($id);

        return response()->json([
            'success' => true,
            'capacity' => $capacity
        ]);
    }

    /**
     * Retrieves the total number of rooms in a dormitory.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDormRoomCount($id)
    {
        $rooms = (new Dorm)->getDormRoomCount($id);

        return response()->json([
            'success' => true,
            'rooms' => $rooms
        ]);
    }
}
