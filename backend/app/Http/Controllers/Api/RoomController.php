<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Room;

class RoomController extends Controller
{
    /**
     * Retrieves all rooms.
     *
     * This endpoint returns a list of all dorm rooms stored in the system.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllRooms()
    {
        $rooms = (new Room)->getAllRooms();

        return response()->json([
            'success' => true,
            'data' => $rooms
        ]);
    }

    /**
     * Retrieves a room by its unique identifier.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRoomById($id)
    {
        $room = (new Room)->getRoomById($id);

        return response()->json([
            'success' => true,
            'data' => $room
        ]);
    }

    /**
     * Retrieves all rooms belonging to a specific dormitory.
     *
     * @param int $dormId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRoomsByDormId($dormId)
    {
        $rooms = (new Room)->getRoomsByDormId($dormId);

        return response()->json([
            'success' => true,
            'data' => $rooms
        ]);
    }

    /**
     * Searches rooms by room number using partial string matching.
     *
     * This endpoint supports quick lookup of rooms within dormitories.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchRooms(Request $request)
    {
        $request->validate([
            'number' => 'required|string|min:1',
        ]);

        $term = $request->query('number');

        $rooms = (new Room)->searchRoomsByNumber($term);

        return response()->json([
            'success' => true,
            'data' => $rooms
        ]);
    }

    /**
     * Creates and stores a new room.
     *
     * Validates room data and associates the room with a dormitory.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function loadRoom(Request $request)
    {
        $validated = $request->validate([
            'dorm_id' => 'required|integer|exists:dorms,id',
            'room_number' => 'required|string',
            'capacity' => 'required|integer|min:1',
            'floor' => 'nullable|integer',
            'description' => 'nullable|string',
        ]);

        $room = (new Room)->loadRoom($validated);

        return response()->json([
            'success' => true,
            'message' => 'Room successfully added.',
            'data' => $room
        ], 201);
    }

    /**
     * Updates an existing room.
     *
     * Only the provided fields are updated.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateRoom(Request $request, $id)
    {
        $validated = $request->validate([
            'dorm_id' => 'nullable|integer|exists:dorms,id',
            'room_number' => 'nullable|string',
            'capacity' => 'nullable|integer|min:1',
            'floor' => 'nullable|integer',
            'description' => 'nullable|string',
        ]);

        $room = (new Room)->updateRoom($id, $validated);

        return response()->json([
            'success' => true,
            'message' => 'Room updated successfully.',
            'data' => $room
        ]);
    }

    /**
     * Deletes a room.
     *
     * This operation permanently removes the room record.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteRoom($id)
    {
        (new Room)->deleteRoom($id);

        return response()->json([
            'success' => true,
            'message' => 'Room deleted successfully.'
        ]);
    }

    /**
     * Retrieves the maximum occupancy capacity of a room.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRoomCapacity($id)
    {
        $capacity = (new Room)->getRoomCapacity($id);

        return response()->json([
            'success' => true,
            'capacity' => $capacity
        ]);
    }
}
