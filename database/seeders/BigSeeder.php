<?php

namespace Database\Seeders;

use App\Models\Genre;
use App\Models\Listing;
use App\Models\Tag;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BigSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'prezes',
            'email' => config('testing_data.email'),
        ]);
        User::factory(10)->create();
        $this->call([
            CurrencySeeder::class,
            TagSeeder::class,
            GenreSeeder::class,
            ListingSeeder::class,
        ]);

        foreach (Listing::all() as $listing) {
            $listing->tags()->attach(Tag::all()->random());
            $listing->genres()->attach(Genre::all()->random());
        }

        foreach (User::all() as $user) {
            Listing::factory(100)->for($user)->published()->create();
        }

    }
}
