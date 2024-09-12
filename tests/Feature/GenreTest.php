<?php

namespace Tests\Feature;

use App\Models\Genre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GenreTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_list_all_genres(): void
    {
        $response = $this->get('/api/genres');

        $response->assertStatus(200);

        $genres = Genre::orderBy('name')->get();

        $response->assertJson($genres->toArray());
    }
}
