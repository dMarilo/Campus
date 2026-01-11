<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Building;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BuildingController extends Controller
{
    /**
     * Building model instance.
     * Used to delegate data access and business logic
     * related to campus buildings.
     */
    protected Building $building;

    /**
     * Injects the Building model into the controller.
     *
     * @param Building $building
     */
    public function __construct(Building $building)
    {
        $this->building = $building;
    }

    /**
     * Retrieves a list of all active buildings.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return response()->json(
            $this->building->getAllBuildings()
        );
    }

    /**
     * Retrieves a single building by its unique code.
     *
     * If the building does not exist or is inactive,
     * a 404 response is returned.
     *
     * @param string $code
     * @return JsonResponse
     */
    public function showByCode(string $code): JsonResponse
    {
        $building = $this->building->getBuildingByCode($code);

        if (!$building) {
            return response()->json(['message' => 'Building not found'], 404);
        }

        return response()->json($building);
    }

    /**
     * Creates a new building record.
     *
     * The request data is passed to the Building model,
     * which handles persistence.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $building = $this->building->createBuilding(
            $request->only([
                'name',
                'code',
                'address',
                'number_of_floors',
                'description',
                'active'
            ])
        );

        return response()->json($building, 201);
    }

    /**
     * Updates an existing building.
     *
     * If the building does not exist, a 404 response is returned.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $building = $this->building->updateBuilding(
            $id,
            $request->only([
                'name',
                'code',
                'address',
                'number_of_floors',
                'description',
                'active'
            ])
        );

        if (!$building) {
            return response()->json(['message' => 'Building not found'], 404);
        }

        return response()->json($building);
    }

    /**
     * Deactivates a building instead of permanently deleting it.
     *
     * This logical delete preserves historical references
     * while removing the building from active use.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->building->deleteBuilding($id);

        if (!$deleted) {
            return response()->json(['message' => 'Building not found'], 404);
        }

        return response()->json(['message' => 'Building deactivated']);
    }
}
