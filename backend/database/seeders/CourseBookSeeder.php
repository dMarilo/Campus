<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CourseBookSeeder extends Seeder
{
    public function run(): void
    {
        $courseBookMappings = [
            // Semester I
            // OET-1 (Osnovi elektrotehnike - 1) -> Osnovi Elektrotehnike 1
            3 => [1],

            // ORT (Osnovi racunarske tehnike) -> Programiranje u C jeziku
            4 => [6],

            // Semester II
            // UVP (Uvod u programiranje) -> Programiranje u C jeziku, Uvod u Algoritme i Strukture Podataka
            8 => [6, 7],

            // OET-2 (Osnovi elektrotehnike - 2) -> Osnovi Elektrotehnike 2
            9 => [2],

            // APS (Aplikativni softver) -> Programiranje u C jeziku
            11 => [6],

            // Semester III
            // TEK-1 (Teorija elektricnih kola - 1) -> Osnovi Elektrotehnike 2, Teorija Signala i Sistema
            14 => [2, 8],

            // ELM (Elektricna mjerenja) -> Osnovi Elektrotehnike 1, Osnovi Elektrotehnike 2
            15 => [1, 2],

            // ELE-1 (Elektronika - 1) -> Elektronski Elementi i Kola, Analogna Elektronika
            16 => [3, 4],

            // PRJ (Programski jezici) -> Programiranje u C jeziku, Uvod u Algoritme i Strukture Podataka
            17 => [6, 7],

            // Semester IV
            // TEK-2 (Teorija elektricnih kola - 2) -> Teorija Signala i Sistema, Digitalna Obrada Signala
            20 => [8, 9],

            // ELE-2 (Elektronika - 2) -> Analogna Elektronika, Digitalna Elektronika
            22 => [4, 5],

            // OOP (Objektno orijentisano programiranje) -> Uvod u Algoritme i Strukture Podataka
            23 => [7],
        ];

        foreach ($courseBookMappings as $courseId => $bookIds) {
            foreach ($bookIds as $bookId) {
                DB::table('course_book')->insert([
                    'course_id' => $courseId,
                    'book_id' => $bookId,
                    'mandatory' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
