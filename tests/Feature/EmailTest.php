<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EmailTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_user_can_update_email(): void
    {
        $user = User::factory()->create(['email' => null]);
        /** @var Authenticatable $user */
        $this->actingAs($user);

        $newEmail = $this->faker()->safeEmail;
        $response = $this->post('/api/email', [
            'email' => $newEmail,
            'email_confirmation' => $newEmail,
        ]);

        $response->assertStatus(200);
        /** @var User $user */
        $user->refresh();
        $this->assertEquals($newEmail, $user->email);
        $response->assertJsonStructure(['name', 'email', 'created_at', 'updated_at']);
    }

    public function test_user_cant_update_email_if_they_already_have_one(): void
    {
        $user = User::factory()->create(['email' => 'notTheNewEmail@example.com']);
        /** @var Authenticatable $user */
        $this->actingAs($user);

        $newEmail = $this->faker()->safeEmail;
        $response = $this->post('/api/email', [
            'email' => $newEmail,
            'email_confirmation' => $newEmail,
        ]);

        $response->assertStatus(400);
        /** @var User $user */
        $user->refresh();
        $this->assertNotEquals($newEmail, $user->email);
    }

    public function test_user_cant_update_email_with_wrong_confirmation(): void
    {
        $user = User::factory()->create(['email' => null]);
        /** @var Authenticatable $user */
        $this->actingAs($user);

        $newEmail = $this->faker()->safeEmail;
        $headers = ['accept' => 'application/json'];
        $response = $this->post('/api/email', [
            'email' => $newEmail,
            'email_confirmation' => 'incorrectEmail@example.com',
        ], $headers);

        $response->assertStatus(422);
        /** @var User $user */
        $user->refresh();
        $this->assertNotEquals($newEmail, $user->email);
    }
}
