<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClassroomSeeder extends Seeder
{
    public function run(): void
    {
        $buildings = DB::table('buildings')->get();

        if ($buildings->count() !== 2) {
            $this->command->warn('Expected exactly 2 buildings. Seeder skipped.');
            return;
        }

        foreach ($buildings as $building) {
            DB::table('classrooms')->insert([
                [
                    'building_id' => $building->id,
                    'name'        => 'A-101',
                    'capacity'    => 30,
                    'type'        => 'classroom',
                    'status'      => 'empty',
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ],
                [
                    'building_id' => $building->id,
                    'name'        => 'A-102',
                    'capacity'    => 25,
                    'type'        => 'computer_room',
                    'status'      => 'empty',
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ],
                [
                    'building_id' => $building->id,
                    'name'        => 'LH-1',
                    'capacity'    => 120,
                    'type'        => 'lecture_hall',
                    'status'      => 'empty',
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ],
                [
                    'building_id' => $building->id,
                    'name'        => 'Lab-1',
                    'capacity'    => 20,
                    'type'        => 'lab',
                    'status'      => 'empty',
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ],
            ]);
        }
    }
}
