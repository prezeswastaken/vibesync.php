<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LikesTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_like_a_listing(): void
    {
        $user = User::factory()->create();

        $initialLikedListingsCount = $user->likedListings->count();

        $this->actingAs($user);

        $response = $this->post('/api/listings/1/like');
        $response->assertStatus(201);
        $user->refresh();
        $this->assertGreaterThan($initialLikedListingsCount, $user->likedListings->count());

        $response = $this->post('/api/listings/1/like');
        $response->assertStatus(200);
        $user->refresh();
        $this->assertEquals($initialLikedListingsCount, $user->likedListings->count());

    }

    public function test_user_can_dislike_a_listing(): void
    {
        $user = User::factory()->create();

        $initialDislikedListingsCount = $user->dislikedListings->count();

        $this->actingAs($user);

        $this->post('/api/listings/1/like');
        $user->refresh();
        $this->assertTrue($user->likedListings->contains(1));

        $response = $this->post('/api/listings/1/dislike');
        $response->assertStatus(201);
        $user->refresh();
        $this->assertGreaterThan($initialDislikedListingsCount, $user->dislikedListings->count());

        $this->assertFalse($user->likedListings->contains(1));

        $response = $this->post('/api/listings/1/dislike');
        $response->assertStatus(200);
        $user->refresh();
        $this->assertEquals($initialDislikedListingsCount, $user->dislikedListings->count());

    }
}
