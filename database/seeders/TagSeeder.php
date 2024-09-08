<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tag_names = ['Mixing', 'Mastering', 'Beatmaking', 'Vocal Tuning', 'Sound Design', 'Recording', 'Editing', 'Composing', 'Arranging', 'Producing'];
        foreach ($tag_names as $tag_name) {
            \App\Models\Tag::create(['name' => $tag_name]);
        }
    }
}
