<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register(): void
    {
        $userData = [
            'email' => 'penes@com.pl',
            'password' => 'password',
            'password_confirmation' => 'password',
            'name' => 'Piotr',
        ];

        $response = $this->post('/api/register', $userData);

        $response->assertStatus(200);

        $this->assertDatabaseHas('users', ['email' => $userData['email'], 'name' => $userData['name']]);

        $response->assertJsonStructure(['access_token', 'token_type', 'expires_in']);
    }
}
