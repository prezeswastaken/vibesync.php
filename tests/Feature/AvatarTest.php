<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class AvatarTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_set_avatar(): void
    {
        /**
         * @var Authenticatable $user
         */
        $user = User::factory()->create();

        $this->actingAs($user);

        $fakeImage = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->postJson('/api/avatars', [
            'avatar' => $fakeImage,
        ]);

        $response->assertStatus(201);

        $this->assertNotNull($user->avatar_url);

        $this->assertStringContainsString('http', $user->avatar_url);

    }
}
