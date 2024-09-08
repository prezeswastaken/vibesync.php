<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'prezes',
            'email' => config('testing_data.email'),
        ]);
        // User::factory(10)->create();
        $this->call([
            TagSeeder::class,
            GenreSeeder::class,
            ListingSeeder::class,
        ]);

    }
}
