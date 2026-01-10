<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Book;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        $books = [

            [
                'title' => 'Osnovi Elektrotehnike 1',
                'author' => 'Milan Petrović',
                'publisher' => 'ETF Istočno Sarajevo',
                'published_year' => 2016,
                'edition' => '2',
                'description' => 'Uvod u elektrotehniku: otpornici, kola, Kirhofovi zakoni, analiza strujnih krugova.',
                'total_copies' => 5,
                'available_copies' => 5,
            ],
            [
                'title' => 'Osnovi Elektrotehnike 2',
                'author' => 'Milan Petrović',
                'publisher' => 'ETF Istočno Sarajevo',
                'published_year' => 2018,
                'edition' => '1',
                'description' => 'Naizmenične struje, kompleksne veličine, fazori, harmonijska analiza.',
                'total_copies' => 5,
                'available_copies' => 5,
            ],
            [
                'title' => 'Elektronski Elementi i Kola',
                'author' => 'Branimir Dolinar',
                'publisher' => 'ETF Beograd',
                'published_year' => 2014,
                'edition' => '4',
                'description' => 'Diodе, tranzistori, pojačavači, operacioni pojačavači.',
                'total_copies' => 4,
                'available_copies' => 4,
            ],
            [
                'title' => 'Analogna Elektronika',
                'author' => 'Dragan Djurić',
                'publisher' => 'ETF Beograd',
                'published_year' => 2017,
                'edition' => '1',
                'description' => 'Analogne komponente, filtri, stabilizatori i pojačavači.',
                'total_copies' => 4,
                'available_copies' => 4,
            ],
            [
                'title' => 'Digitalna Elektronika',
                'author' => 'Zoran Uzelac',
                'publisher' => 'ETF Banja Luka',
                'published_year' => 2020,
                'edition' => '1',
                'description' => 'Logička kola, kombinatorna i sekvencijalna logika, brojni sistemi.',
                'total_copies' => 3,
                'available_copies' => 3,
            ],
            [
                'title' => 'Programiranje u C jeziku',
                'author' => 'Kernighan & Ritchie',
                'publisher' => 'Mikro knjiga',
                'published_year' => 2008,
                'edition' => '3',
                'description' => 'Osnovna struktura C jezika, pokazivači, memorija, standardna biblioteka.',
                'total_copies' => 6,
                'available_copies' => 6,
            ],
            [
                'title' => 'Uvod u Algoritme i Strukture Podataka',
                'author' => 'Marko Marković',
                'publisher' => 'ETF Istočno Sarajevo',
                'published_year' => 2021,
                'edition' => '1',
                'description' => 'Liste, stabla, grafovi, sortiranje i pretrage.',
                'total_copies' => 4,
                'available_copies' => 4,
            ],
            [
                'title' => 'Teorija Signala i Sistema',
                'author' => 'Zlatko Stanković',
                'publisher' => 'ETF Beograd',
                'published_year' => 2015,
                'edition' => '1',
                'description' => 'Kontinuirani i diskretni signali, transformacije i analiza.',
                'total_copies' => 3,
                'available_copies' => 3,
            ],
            [
                'title' => 'Digitalna Obrada Signala',
                'author' => 'Alan V. Oppenheim',
                'publisher' => 'Mikro knjiga',
                'published_year' => 2016,
                'edition' => '2',
                'description' => 'Furijeove transformacije, filteri, obrada signala u realnom vremenu.',
                'total_copies' => 3,
                'available_copies' => 3,
            ],
            [
                'title' => 'Telekomunikacioni Sistemi',
                'author' => 'Slobodan Miljanović',
                'publisher' => 'ETF Banja Luka',
                'published_year' => 2019,
                'edition' => '1',
                'description' => 'Modulacija, multipleksiranje, mreže, komunikacioni protokoli.',
                'total_copies' => 4,
                'available_copies' => 4,
            ],
        ];

        foreach ($books as $data) {
            Book::create($data);
        }
    }
}
