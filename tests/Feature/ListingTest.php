<?php

namespace Tests\Feature;

use App\Exceptions\ListingException;
use App\Models\Listing;
use App\Models\User;
use Auth;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Str;
use Tests\TestCase;

class ListingTest extends TestCase
{
    use RefreshDatabase;

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
            'currency_id' => 1,
            'tag_ids' => [1, 2, 3],
            'genre_ids' => [1, 2, 3],
        ];

        $response = $this->post('/api/listings', $listingRequest);

        $response->assertStatus(201);

        $this->assertDatabaseHas('listings', ['title' => $listingRequest['title'], 'body' => $listingRequest['body']]);

        $model = Listing::find($response->json()['id']);

        $this->assertNotNull($model);
        $this->assertEquals(Str::apa($listingRequest['title']), $model->title);
        $this->assertEquals($listingRequest['body'], $model->body);
        $this->assertEquals($listingRequest['is_sale_offer'], $model->is_sale_offer);
        $this->assertEquals($listingRequest['price'], $model->price->amount);
        $this->assertEquals($user->id, $model->user_id);

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

        $listings = $response->json()['data'];

        $this->assertNotEmpty($listings, 'The listings array is empty'); // Ensure listings exist

        foreach ($listings as $listing) {
            $this->assertArrayHasKey('author', $listing);
            $this->assertArrayHasKey('title', $listing);
            $this->assertArrayHasKey('body', $listing);
            $this->assertArrayHasKey('is_sale_offer', $listing, 'Listing is missing "is_sale_offer" key');
            $this->assertArrayHasKey('price', $listing);
            $this->assertArrayHasKey('tags', $listing);
            $this->assertArrayHasKey('genres', $listing);
        }
    }

    public function test_user_can_publish_their_listing(): void
    {
        $user = User::find(1);
        Auth::login($user);
        $listing = Listing::factory()->create(['user_id' => $user->id, 'is_published' => false]);

        $response = $this->post("/api/listings/{$listing->id}/publish");

        $response->assertStatus(200);

        $listing->refresh();

        $this->assertTrue($listing->is_published == true);
    }

    public function test_user_cant_publish_other_users_listing(): void
    {
        $user = User::find(1);
        Auth::login($user);
        $listing = Listing::factory()->create(['user_id' => 2, 'is_published' => false]);

        $response = $this->post("/api/listings/{$listing->id}/publish");

        $response->assertStatus(403);
    }

    public function test_user_can_unpublish_their_listing(): void
    {
        $user = User::find(1);
        Auth::login($user);
        $listing = Listing::factory()->create(['user_id' => $user->id, 'is_published' => true]);

        $response = $this->post("/api/listings/{$listing->id}/unpublish");

        $response->assertStatus(200);

        $listing->refresh();

        $this->assertTrue($listing->is_published == false);
    }

    public function test_user_cant_unpublish_other_users_listing(): void
    {
        $user = User::find(1);
        Auth::login($user);
        $listing = Listing::factory()->create(['user_id' => 2, 'is_published' => true]);

        $response = $this->post("/api/listings/{$listing->id}/unpublish");

        $response->assertStatus(403);
    }

    public function test_user_can_list_their_listings(): void
    {
        $user = User::find(1);
        Auth::login($user);
        $response = $this->get('/api/my/listings');

        $response->assertStatus(200);

        $listings = $response->json()['data'];

        $this->assertNotEmpty($listings, 'The listings array is empty');

        foreach ($listings as $listing) {
            $this->assertArrayHasKey('author', $listing);
            $this->assertArrayHasKey('title', $listing);
            $this->assertArrayHasKey('body', $listing);
            $this->assertArrayHasKey('is_sale_offer', $listing, 'Listing is missing "is_sale_offer" key');
            $this->assertArrayHasKey('price', $listing);
            $this->assertArrayHasKey('tags', $listing);
            $this->assertArrayHasKey('genres', $listing);
        }

        $this->assertEquals(count($listings), $user->listings->count());
    }

    public function test_user_can_show_their_listing(): void
    {
        $user = User::find(1);
        Auth::login($user);
        $listing = Listing::factory()->create(['user_id' => $user->id]);

        $response = $this->get("/api/listings/{$listing->id}");

        $response->assertStatus(200);

        $listing = $response->json();

        $this->assertArrayHasKey('author', $listing);
        $this->assertArrayHasKey('title', $listing);
        $this->assertArrayHasKey('body', $listing);
        $this->assertArrayHasKey('is_sale_offer', $listing, 'Listing is missing "is_sale_offer" key');
        $this->assertArrayHasKey('price', $listing);
        $this->assertArrayHasKey('tags', $listing);
        $this->assertArrayHasKey('genres', $listing);
    }

    public function test_user_can_show_published_listing_of_any_user(): void
    {
        $user = User::find(1);
        Auth::login($user);
        $listing = Listing::factory()->create(['user_id' => 2, 'is_published' => true]);

        $response = $this->get("/api/listings/{$listing->id}");

        $response->assertStatus(200);

        $listing = $response->json();

        $this->assertArrayHasKey('author', $listing);
        $this->assertArrayHasKey('title', $listing);
        $this->assertArrayHasKey('body', $listing);
        $this->assertArrayHasKey('is_sale_offer', $listing);
        $this->assertArrayHasKey('price', $listing);
        $this->assertArrayHasKey('tags', $listing);
        $this->assertArrayHasKey('genres', $listing);
    }

    public function test_user_cant_show_unpublished_listing_of_other_user(): void
    {
        $user = User::find(1);
        Auth::login($user);
        $listing = Listing::factory()->create(['user_id' => 2, 'is_published' => false]);

        $response = $this->get("/api/listings/{$listing->id}");

        $response->assertStatus(ListingException::notFound()->getCode());
        $response->assertJson(['message' => ListingException::notFound()->getMessage()]);
    }

    public function test_user_can_edit_their_listing(): void
    {
        $user = User::find(1);
        Auth::login($user);
        $listing = Listing::factory()->create(['user_id' => $user->id]);

        $newListingData = [
            'title' => 'New great title',
            'body' => 'New body',
            'is_sale_offer' => true,
            'price' => 100,
            'currency_id' => 3,
            'tag_ids' => [1, 2, 3],
            'genre_ids' => [1, 2, 3],
        ];

        $response = $this->put("/api/listings/{$listing->id}", $newListingData);

        $listing->refresh();

        $response->assertStatus(200);

        $this->assertEquals(Str::apa($newListingData['title']), $listing->title);
        $this->assertEquals($newListingData['body'], $listing->body);
        $this->assertEquals($newListingData['is_sale_offer'], $listing->is_sale_offer);
        $this->assertEquals($newListingData['price'], $listing->price->amount);
        $this->assertEquals($newListingData['currency_id'], $listing->price->currency_id);

        $listing->tags->each(function ($tag) use ($newListingData) {
            $this->assertContains($tag->id, $newListingData['tag_ids']);
        });
        $listing->genres->each(function ($genre) use ($newListingData) {
            $this->assertContains($genre->id, $newListingData['genre_ids']);
        });

    }

    public function test_user_cant_edit_other_users_listing(): void
    {
        $user = User::find(1);
        Auth::login($user);
        $listing = Listing::factory()->create(['user_id' => 2]);

        $newListingData = [
            'title' => 'New title',
            'body' => 'New body',
            'is_sale_offer' => false,
            'price' => 100,
            'tag_ids' => [1, 2, 3],
            'genre_ids' => [1, 2, 3],
        ];

        $response = $this->put("/api/listings/{$listing->id}", $newListingData);

        $response->assertStatus(ListingException::unauthorized()->getCode());

        $response->assertJson(['message' => ListingException::unauthorized()->getMessage()]);
    }

    public function test_user_can_delete_their_listing(): void
    {
        $user = User::find(1);
        Auth::login($user);
        $listing = Listing::factory()->create(['user_id' => $user->id]);

        $response = $this->delete("/api/listings/{$listing->id}");

        $response->assertStatus(204);

        $this->assertNull(Listing::find($listing->id));
    }

    public function test_user_cant_delete_other_users_listing(): void
    {
        $user = User::find(1);
        Auth::login($user);
        $listing = Listing::factory()->create(['user_id' => 2]);

        $response = $this->delete("/api/listings/{$listing->id}");

        $response->assertStatus(ListingException::unauthorized()->getCode());

        $response->assertJson(['message' => ListingException::unauthorized()->getMessage()]);
    }

    public function test_user_can_show_listings_in_different_currencies(): void
    {
        $user = User::find(1);
        Auth::login($user);
        $listing = Listing::factory()->create(['user_id' => $user->id]);
        $listing->price->currency_id = 1;
        $listing->price->save();

        $response = $this->get("/api/listings/{$listing->id}?currency_id=3");

        $response->assertStatus(200);

        $listingId = $listing->id;

        $listing = $response->json();

        $this->assertArrayHasKey('price', $listing);
        $this->assertArrayHasKey('currency_code', $listing['price']);
        $this->assertArrayHasKey('amount', $listing['price']);

        $this->assertEquals('PLN', $listing['price']['currency_code']);

        $response = $this->get("/api/listings/{$listingId}?currency_id=2");
        $listing = $response->json();

        $this->assertEquals('EUR', $listing['price']['currency_code']);

        $response = $this->get("/api/listings/{$listingId}?currency_id=1");
        $listing = $response->json();

        $this->assertEquals('USD', $listing['price']['currency_code']);
    }
}
