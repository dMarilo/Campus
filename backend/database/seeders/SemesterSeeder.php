<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Semester;
use App\Models\AcademicYear;

class SemesterSeeder extends Seeder
{
    public function run(): void
    {
        $currentYear = AcademicYear::where('is_active', true)->first();

        Semester::insert([
            [
                'academic_year_id' => $currentYear->id,
                'name' => 'Winter',
                'code' => 'W',
                'order' => 1,
            ],
            [
                'academic_year_id' => $currentYear->id,
                'name' => 'Summer',
                'code' => 'S',
                'order' => 2,
            ],
        ]);
    }
}
