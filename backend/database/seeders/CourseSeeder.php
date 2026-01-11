<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $courses = [
            [
                'code'        => 'EE101',
                'name'        => 'Introduction to Electrical Engineering',
                'description' => 'Fundamental concepts of electrical engineering.',
                'ects'        => 6,
                'department'  => 'Electrical Engineering',
                'level'       => 'bachelor',
                'mandatory'   => true,
                'status'      => 'active',
            ],
            [
                'code'        => 'EE201',
                'name'        => 'Circuit Theory',
                'description' => 'Analysis of electrical circuits.',
                'ects'        => 7,
                'department'  => 'Electrical Engineering',
                'level'       => 'bachelor',
                'mandatory'   => true,
                'status'      => 'active',
            ],
            [
                'code'        => 'EE301',
                'name'        => 'Digital Signal Processing',
                'description' => 'Signals, systems, and digital filters.',
                'ects'        => 6,
                'department'  => 'Electrical Engineering',
                'level'       => 'master',
                'mandatory'   => false,
                'status'      => 'active',
            ],
        ];

        foreach ($courses as $course) {
            Course::updateOrCreate(
                ['code' => $course['code']],
                $course
            );
        }
    }
}
