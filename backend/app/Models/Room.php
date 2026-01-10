<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    /**
     * Mass-assignable attributes for the Room model.
     * Represents structural and capacity-related information
     * for a dormitory room.
     */
    protected $fillable = [
        'dorm_id',
        'room_number',
        'capacity',
        'floor',
        'description',
    ];

    /**
     * Defines the relationship between a room and its dormitory.
     * Each room belongs to exactly one dorm.
     */
    public function dorm()
    {
        return $this->belongsTo(Dorm::class);
    }

    /**
     * Retrieves all rooms from the database.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllRooms()
    {
        return $this->all();
    }

    /**
     * Retrieves a room by its unique identifier.
     * Throws an exception if the room does not exist.
     *
     * @param int $id
     * @return Room
     */
    public function getRoomById(int $id)
    {
        return $this->findOrFail($id);
    }

    /**
     * Retrieves all rooms belonging to a specific dormitory.
     *
     * @param int $dormId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRoomsByDormId(int $dormId)
    {
        return $this->where('dorm_id', $dormId)->get();
    }

    /**
     * Searches rooms by room number using partial string matching.
     *
     * @param string $query
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function searchRoomsByNumber(string $query)
    {
        return $this->where('room_number', 'LIKE', '%' . $query . '%')->get();
    }

    /**
     * Creates and stores a new room record in the database.
     *
     * @param array $data
     * @return Room
     */
    public function loadRoom(array $data)
    {
        return $this->create($data);
    }

    /**
     * Updates an existing room with new data.
     * Throws an exception if the room does not exist.
     *
     * @param int $id
     * @param array $data
     * @return Room
     */
    public function updateRoom(int $id, array $data)
    {
        $room = $this->findOrFail($id);
        $room->update($data);
        return $room;
    }

    /**
     * Deletes a room record from the database.
     * This operation permanently removes the room.
     *
     * @param int $id
     * @return bool|null
     */
    public function deleteRoom(int $id)
    {
        $room = $this->findOrFail($id);
        return $room->delete();
    }

    /**
     * Retrieves the maximum occupancy capacity of a room.
     *
     * @param int $id
     * @return int
     */
    public function getRoomCapacity(int $id)
    {
        return $this->findOrFail($id)->capacity;
    }
}
