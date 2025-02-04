<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchUsersTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_search_users_by_email_or_name(): void
    {
        $user = User::factory()->create(['email' => 'test@example.com', 'name' => 'John Doe']);
        $matchingEmailUser = User::factory()->create(['email' => 'elephant@example.com']);
        $matchingNameUser = User::factory()->create(['name' => 'Elephant']);
        User::factory()->create(['email' => 'other@example.com', 'name' => 'Other User']);

        /** @var Authenticatable $user */
        $this->actingAs($user);

        $response = $this->get('/api/search-users?q=elephant');

        $response->assertStatus(200)
            ->assertJsonCount(2)
            ->assertJsonStructure([
                '*' => ['id', 'name', 'email', 'created_at', 'updated_at'],
            ])
            ->assertJsonFragment(['email' => $matchingEmailUser->email])
            ->assertJsonFragment(['name' => $matchingNameUser->name]);
    }

    public function test_search_returns_empty_array_when_no_matches(): void
    {
        $user = User::factory()->create();
        /** @var Authenticatable $user */
        $this->actingAs($user);

        $response = $this->get('/api/search-users?q=nonexistent');

        $response->assertStatus(200)
            ->assertJsonCount(0);
    }
}
