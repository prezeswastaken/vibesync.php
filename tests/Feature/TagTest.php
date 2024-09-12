<?php

namespace Tests\Feature;

use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_list_all_tags(): void
    {
        $response = $this->get('/api/tags');

        $response->assertStatus(200);

        $tags = Tag::orderBy('name')->get();

        $response->assertJson($tags->toArray());
    }
}
