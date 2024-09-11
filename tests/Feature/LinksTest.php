<?php

namespace Tests\Feature;

use App\Exceptions\ListingException;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LinksTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_add_links_to_their_listing(): void
    {
        $listing = Listing::find(1);
        $user = $listing->user;
        $this->actingAs($user);
        $initial_links_count = $listing->links->count();

        $linkData = [
            'title' => 'Google',
            'url' => 'https://www.google.com',
            'description' => 'Search engine',
        ];

        $response = $this->postJson("/api/listings/{$listing->id}/links", $linkData);

        $response->assertStatus(201);

        $listing->refresh();

        $this->assertCount($initial_links_count + 1, $listing->links);

    }

    public function test_user_cant_add_links_to_other_users_listings(): void
    {
        $listing = Listing::find(1);
        $user = User::factory()->create();
        $this->actingAs($user);

        $linkData = [
            'title' => 'Google',
            'url' => 'https://www.google.com',
            'description' => 'Search engine',
        ];

        $response = $this->postJson("/api/listings/{$listing->id}/links", $linkData);

        $response->assertStatus(ListingException::unauthorized()->getCode());

        $response->assertJson([
            'message' => ListingException::unauthorized()->getMessage(),
        ]);
    }
}
