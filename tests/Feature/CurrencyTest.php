<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CurrencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_list_all_currencies(): void
    {
        $user = User::factory()->create();

        /** @var Authenticatable $user */
        $this->actingAs($user);

        $response = $this->get('/api/currencies');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            '*' => ['id', 'code', 'rate_to_usd', 'created_at', 'updated_at'],
        ]);
    }
}
