<?php

namespace Database\Seeders;

use App\Models\Listing;
use App\Models\User;
use Illuminate\Database\Seeder;

class ListingSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        Listing::factory(5)->for($user)->create();
        Listing::factory(5)->for($user)->published()->create();
    }
}
