<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Room;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        $rooms = [

            // Dorm 2
            ['dorm_id' => 2, 'room_number' => 'B12', 'capacity' => 2, 'floor' => 1, 'description' => 'Soba sa balkonom'],
            ['dorm_id' => 2, 'room_number' => 'B21', 'capacity' => 1, 'floor' => 2, 'description' => 'Renovirana soba'],

            // Dorm 3
            ['dorm_id' => 3, 'room_number' => 'P10', 'capacity' => 2, 'floor' => 1],
            ['dorm_id' => 3, 'room_number' => 'P11', 'capacity' => 3, 'floor' => 1],

            // Dorm 4
            ['dorm_id' => 4, 'room_number' => 'L05', 'capacity' => 4, 'floor' => 0, 'description' => 'Četvorokrevetna soba'],

            // Dorm 5
            ['dorm_id' => 5, 'room_number' => 'D101', 'capacity' => 2, 'floor' => 1],
            ['dorm_id' => 5, 'room_number' => 'D102', 'capacity' => 2, 'floor' => 1],
        ];

        foreach ($rooms as $room) {
            Room::create($room);
        }
    }
}
