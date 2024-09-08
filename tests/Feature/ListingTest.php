<?php

namespace Tests\Feature;

use App\Models\Listing;
use App\Models\User;
use Auth;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_user_can_create_listing(): void
    {
        $user = User::factory()->create();
        Auth::login($user);
        $this->assertNull($user->listings->first());
        $listingRequest = [
            'title' => 'Test title',
            'body' => 'Test body. This is a really long body',
            'is_sale_offer' => true,
            'price' => 60,
            'tag_ids' => [1, 2, 3],
            'genre_ids' => [1, 2, 3],
        ];

        $response = $this->post('/api/listings', $listingRequest);

        $response->assertStatus(201);

        $this->assertDatabaseHas('listings', ['title' => $listingRequest['title'], 'body' => $listingRequest['body']]);

        $model = Listing::find($response->json()['id']);

        $this->assertNotNull($model);
        $this->assertEquals($listingRequest['title'], $model->title);
        $this->assertEquals($listingRequest['body'], $model->body);
        $this->assertEquals($listingRequest['is_sale_offer'], $model->is_sale_offer);
        $this->assertEquals($listingRequest['price'], $model->price);

        $model->tags->each(function ($tag) use ($listingRequest) {
            $this->assertContains($tag->id, $listingRequest['tag_ids']);
        });
        $model->genres->each(function ($genre) use ($listingRequest) {
            $this->assertContains($genre->id, $listingRequest['genre_ids']);
        });

        $user = User::find($user->id)->load('listings')->first();
        $this->assertNotNull($user->listings->first()->tags);
    }

    public function test_user_cant_create_listing_with_invalid_request(): void
    {
        $user = User::find(1);
        Auth::login($user);
        $listingRequest = [
            'title' => 'Test title',
            'body' => 'Test body. This is a really long body',
            'price' => 60,
        ];

        $response = $this->post('/api/listings', $listingRequest);

        $response->assertStatus(302);

        $listingRequest = [
            'title' => 'Test title',
            'body' => 'Test body. This is a really long body',
            'tags_ids' => [1, 2, 3],
            'genre_ids' => [1, 2, 3],
        ];

        $response = $this->post('/api/listings', $listingRequest);

        $response->assertStatus(302);
    }

    public function test_user_can_show_all_listings(): void
    {
        $user = User::find(1);
        Auth::login($user);
        $response = $this->get('/api/listings');

        $response->assertStatus(200);

        $listings = $response->json();

        $this->assertNotEmpty($listings, 'The listings array is empty'); // Ensure listings exist

        foreach ($listings as $listing) {
            $this->assertArrayHasKey('title', $listing);
            $this->assertArrayHasKey('body', $listing);
            $this->assertArrayHasKey('is_sale_offer', $listing, 'Listing is missing "is_sale_offer" key');
            $this->assertArrayHasKey('price', $listing);
            $this->assertArrayHasKey('tags', $listing);
            $this->assertArrayHasKey('genres', $listing);
        }
    }
}
