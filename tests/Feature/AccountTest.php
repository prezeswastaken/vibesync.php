<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

const headers = ['accept' => 'application/json'];

class AccountTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_user_can_update_name(): void
    {
        $user = User::factory()->create();
        /** @var Authenticatable $user */
        $this->actingAs($user);

        $newName = $this->faker()->name;
        $response = $this->patch('/api/account', ['name' => $newName], headers);

        /** @var User $user */
        $user->refresh();
        $this->assertEquals($newName, $user->name);

        $response->assertStatus(200);
    }

    public function test_user_can_update_password(): void
    {
        $user = User::factory()->create();
        /** @var Authenticatable $user */
        $this->actingAs($user);

        $newPassword = $this->faker()->password(8);
        $response = $this->patch('/api/account', [
            'current_password' => 'password',
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ]);

        /** @var User $user */
        $user->refresh();
        $this->assertTrue(password_verify($newPassword, $user->password));

        $response->assertJsonStructure(['name', 'email', 'created_at', 'updated_at']);

        $response->assertStatus(200);
    }

    public function test_user_cant_update_password_with_wrong_current_password(): void
    {
        $user = User::factory()->create();
        /** @var Authenticatable $user */
        $this->actingAs($user);

        $newPassword = $this->faker()->password(8);
        $response = $this->patch('/api/account', [
            'current_password' => 'wrong_password',
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ]);

        /** @var User $user */
        $user->refresh();
        $this->assertFalse(password_verify($newPassword, $user->password));

        $response->assertStatus(400);
    }

    public function test_user_cant_update_password_with_wrong_confirmation(): void
    {
        $user = User::factory()->create();
        /** @var Authenticatable $user */
        $this->actingAs($user);

        $newPassword = $this->faker()->password(8);
        $response = $this->patch('/api/account', [
            'current_password' => 'password',
            'password' => $newPassword,
            'password_confirmation' => 'wrong_confirmation',
        ], headers);

        /** @var User $user */
        $user->refresh();
        $this->assertFalse(password_verify($newPassword, $user->password));

        $response->assertStatus(422);
    }

    public function test_user_can_update_email(): void
    {
        $user = User::factory()->create();
        /** @var Authenticatable $user */
        $this->actingAs($user);

        $newEmail = $this->faker()->safeEmail;
        $response = $this->patch('/api/account', [
            'current_email' => $user->email,
            'email' => $newEmail,
            'email_confirmation' => $newEmail,
        ]);

        /** @var User $user */
        $user->refresh();
        $this->assertEquals($newEmail, $user->email);
        $response->assertJsonStructure(['name', 'email', 'created_at', 'updated_at']);
        $response->assertStatus(200);
    }

    public function test_user_cant_update_email_with_wrong_current_email(): void
    {
        $user = User::factory()->create();
        /** @var Authenticatable $user */
        $this->actingAs($user);

        $newEmail = $this->faker()->safeEmail;
        $response = $this->patch('/api/account', [
            'current_email' => 'wrong@email.com',
            'email' => $newEmail,
            'email_confirmation' => $newEmail,
        ]);

        /** @var User $user */
        $user->refresh();
        $this->assertNotEquals($newEmail, $user->email);
        $response->assertStatus(400);
    }

    public function test_user_cant_update_email_with_wrong_confirmation(): void
    {
        $user = User::factory()->create();
        /** @var Authenticatable $user */
        $this->actingAs($user);

        $newEmail = $this->faker()->safeEmail;
        $response = $this->patch('/api/account', [
            'current_email' => $user->email,
            'email' => $newEmail,
            'email_confirmation' => 'wrong@email.com',
        ], headers);

        /** @var User $user */
        $user->refresh();
        $this->assertNotEquals($newEmail, $user->email);
        $response->assertStatus(422);
    }

    public function test_user_cant_update_email_to_existing_email(): void
    {
        $user = User::factory()->create();
        $existingUser = User::factory()->create();
        /** @var Authenticatable $user */
        $this->actingAs($user);

        $response = $this->patch('/api/account', [
            'current_email' => $user->email,
            'email' => $existingUser->email,
            'email_confirmation' => $existingUser->email,
        ], headers);

        /** @var User $user */
        $user->refresh();
        $this->assertNotEquals($existingUser->email, $user->email);
        $response->assertStatus(422);
    }
}
