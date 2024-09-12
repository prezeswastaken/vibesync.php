<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class GenreSeeder extends Seeder
{
    public function run(): void
    {
        $genre_names = [
            'HipHop',
            'Trap',
            'Pop',
            'Rock',
            'Jazz',
            'Blues',
            'Country',
            'Classical',
            'Electronic',
            'Folk',
            'R&B',
            'Reggae',
            'Metal',
            'Punk',
            'Indie',
            'Soul',
            'Gospel',
            'Latin',
            'World',
            'New Age',
            'Soundtrack',
            'Comedy',
            'Children',
            'Holiday',
        ];

        foreach ($genre_names as $genre_name) {
            \App\Models\Genre::create([
                'name' => $genre_name,
            ]);
        }
    }
}
