<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_delete_other_users(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $userToDelete = User::factory()->create();

        $this->actingAs($admin);

        $response = $this->deleteJson("/api/users/{$userToDelete->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('users', ['id' => $userToDelete->id]);
    }

    public function test_admin_cannot_delete_themselves(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin);

        $response = $this->deleteJson("/api/users/{$admin->id}");

        $response->assertStatus(400);
        $response->assertJson(['message' => 'You cannot delete yourself']);
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_non_admin_cannot_delete_users(): void
    {
        $regularUser = User::factory()->create(['is_admin' => false]);
        $userToDelete = User::factory()->create();

        $this->actingAs($regularUser);

        $response = $this->deleteJson("/api/users/{$userToDelete->id}");

        $response->assertStatus(401);
        $this->assertDatabaseHas('users', ['id' => $userToDelete->id]);
    }

    public function test_unauthenticated_user_cannot_delete_users(): void
    {
        $userToDelete = User::factory()->create();

        $response = $this->deleteJson("/api/users/{$userToDelete->id}");

        $response->assertStatus(401);
        $this->assertDatabaseHas('users', ['id' => $userToDelete->id]);
    }
}
