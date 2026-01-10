<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Dorm;

class DormSeeder extends Seeder
{
    public function run(): void
    {
        $dorms = [
            [
                'name' => 'Studentski Dom A',
                'address' => 'Vuka Karadžića 12, Istočno Sarajevo',
                'total_rooms' => 120,
                'total_beds' => 240,
                'description' => 'Glavni dom u Istočnom Sarajevu, renoviran 2020.'
            ],
            [
                'name' => 'Studentski Dom B',
                'address' => 'Filipa Višnjića 8, Istočno Sarajevo',
                'total_rooms' => 80,
                'total_beds' => 160,
                'description' => 'Manji, ali moderan studentski dom sa novom kuhinjom.'
            ],
            [
                'name' => 'Studentski Dom Pale',
                'address' => 'Milana Simovića 33, Pale',
                'total_rooms' => 60,
                'total_beds' => 120,
                'description' => 'Dom za brucoše i studente prve godine.'
            ],
            [
                'name' => 'Studentski Dom Lukavica',
                'address' => 'Srpskih Vladara 5, Lukavica',
                'total_rooms' => 100,
                'total_beds' => 200,
                'description' => 'Dom poznat po velikom dvorištu i studenskim aktivnostima.'
            ],
            [
                'name' => 'Studentski Dom Dobrinja',
                'address' => 'Trg Heroja 1, Dobrinja',
                'total_rooms' => 150,
                'total_beds' => 300,
                'description' => 'Najveći studentski dom, pogodan za strane studente.'
            ],
        ];

        foreach ($dorms as $data) {
            Dorm::create($data);
        }
    }
}
