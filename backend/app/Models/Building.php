<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Building extends Model
{
    /**
     * Mass-assignable attributes for the Building model.
     * Represents core identification, location, and status information
     * for a physical campus building.
     */
    protected $fillable = [
        'name',
        'code',
        'address',
        'number_of_floors',
        'description',
        'active',
    ];

    /**
     * Retrieves all active buildings.
     * Inactive buildings are excluded to preserve historical data
     * while keeping the active dataset clean.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllBuildings()
    {
        return self::where('active', true)->get();
    }

    /**
     * Retrieves a single active building by its unique code.
     *
     * @param string $code
     * @return Building|null
     */
    public function getBuildingByCode(string $code)
    {
        return self::where('code', $code)
            ->where('active', true)
            ->first();
    }

    /**
     * Creates and stores a new building record in the database.
     *
     * @param array $data
     * @return Building
     */
    public function createBuilding(array $data)
    {
        return self::create($data);
    }

    /**
     * Updates an existing building with new data.
     * Returns null if the building does not exist.
     *
     * @param int $id
     * @param array $data
     * @return Building|null
     */
    public function updateBuilding(int $id, array $data)
    {
        $building = self::find($id);

        if (!$building) {
            return null;
        }

        $building->update($data);
        return $building;
    }

    /**
     * Deactivates a building instead of permanently deleting it.
     * This preserves referential integrity and historical records.
     *
     * @param int $id
     * @return bool
     */
    public function deleteBuilding(int $id): bool
    {
        $building = self::find($id);

        if (!$building) {
            return false;
        }

        // Soft delete behavior via active flag
        $building->active = false;
        $building->save();

        return true;
    }
}
