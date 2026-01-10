<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AcademicYear;

class AcademicYearSeeder extends Seeder
{
    public function run(): void
    {
        AcademicYear::insert([
            [
                'name' => '2022/2023',
                'start_date' => '2022-10-01',
                'end_date' => '2023-09-30',
                'is_active' => false,
            ],
            [
                'name' => '2023/2024',
                'start_date' => '2023-10-01',
                'end_date' => '2024-09-30',
                'is_active' => false,
            ],
            [
                'name' => '2024/2025',
                'start_date' => '2024-10-01',
                'end_date' => '2025-09-30',
                'is_active' => false,
            ],
            [
                'name' => '2025/2026',
                'start_date' => '2025-10-01',
                'end_date' => '2026-09-30',
                'is_active' => true, // ✅ CURRENT YEAR
            ],
        ]);
    }
}
