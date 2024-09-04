<?php

namespace Tests\Feature;

use App\Models\User;
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

    public function test_user_cannot_register_with_existing_email(): void
    {
        $userData = [
            'email' => 'penes@gmail.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'name' => 'Piotr',
        ];
        $response = $this->post('/api/register', $userData);
        $second_response = $this->post('/api/register', $userData);
        $second_response->assertStatus(422);
    }

    public function test_user_can_login_and_refresh_tokens(): void
    {
        $userData = [
            'email' => 'test@testowy.com',
            'password' => 'password',
            'name' => 'Piotr',
        ];
        $user = User::create($userData);
        $token_response = $this->post('/api/login', $userData);
        $token_response->assertStatus(200);
        $token_response->assertJsonStructure(['access_token', 'token_type', 'expires_in']);

        $token = $token_response->json('access_token');
        $refresh_response = $this->post('/api/refresh', [], ['Authorization' => 'Bearer '.$token]);

        $refresh_response->assertStatus(200);

        $refreshed_token = $refresh_response->json('access_token');

        $me_response = $this->get('/api/me', [], ['Authorization' => 'Bearer '.$refreshed_token]);
        $me_response->assertStatus(200);
        $me_response->assertJsonFragment(['email' => $userData['email']]);
    }
}
