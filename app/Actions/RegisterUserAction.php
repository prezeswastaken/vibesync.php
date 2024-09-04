<?php

namespace App\Actions;

use App\Models\User;
use Illuminate\Http\Response;

class RegisterUserAction
{
    public function handle(string $email, string $name, string $password): Response|bool
    {
        User::create(['email' => $email, 'name' => $name, 'password' => $password]);
        $credentials = ['email' => $email, 'password' => $password];

        if (! $token = auth()->attempt($credentials)) {

            return response(['error' => 'Unauthorized'], 401);
        }

        return $token;
    }
}
