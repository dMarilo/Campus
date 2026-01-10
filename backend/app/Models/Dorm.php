<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dorm extends Model
{
    /**
     * Mass-assignable attributes for the Dorm model.
     * Represents basic identification, location, and capacity information
     * for a student dormitory.
     */
    protected $fillable = [
        'name',
        'address',
        'total_rooms',
        'total_beds',
        'description',
    ];

    /**
     * Retrieves all dormitories from the database.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllDorms()
    {
        return $this->all();
    }

    /**
     * Retrieves a dormitory by its unique identifier.
     * Throws an exception if the dormitory does not exist.
     *
     * @param int $id
     * @return Dorm
     */
    public function getDormById(int $id)
    {
        return $this->findOrFail($id);
    }

    /**
     * Searches dormitories by name using partial string matching.
     *
     * @param string $search
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function searchDormsByName(string $search)
    {
        return $this->where('name', 'LIKE', '%' . $search . '%')->get();
    }

    /**
     * Creates and stores a new dormitory record in the database.
     *
     * @param array $data
     * @return Dorm
     */
    public function loadDorm(array $data)
    {
        return $this->create($data);
    }

    /**
     * Updates an existing dormitory with new data.
     * Throws an exception if the dormitory does not exist.
     *
     * @param int $id
     * @param array $data
     * @return Dorm
     */
    public function updateDorm(int $id, array $data)
    {
        $dorm = $this->findOrFail($id);
        $dorm->update($data);
        return $dorm;
    }

    /**
     * Deletes a dormitory record from the database.
     * This operation permanently removes the dormitory.
     *
     * @param int $id
     * @return bool|null
     */
    public function deleteDorm(int $id)
    {
        $dorm = $this->findOrFail($id);
        return $dorm->delete();
    }

    /**
     * Retrieves the total bed capacity of a dormitory.
     *
     * @param int $id
     * @return int
     */
    public function getDormCapacity(int $id)
    {
        $dorm = $this->findOrFail($id);
        return $dorm->total_beds;
    }

    /**
     * Retrieves the total number of rooms in a dormitory.
     *
     * @param int $id
     * @return int
     */
    public function getDormRoomCount(int $id)
    {
        $dorm = $this->findOrFail($id);
        return $dorm->total_rooms;
    }
}
