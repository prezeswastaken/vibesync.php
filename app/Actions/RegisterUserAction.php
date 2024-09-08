<?php

namespace App\Actions;

use App\Exceptions\AuthException;
use App\Models\User;
use JWTAuth;

class RegisterUserAction
{
    public function handle(string $email, string $name, string $password): string
    {
        User::create(['email' => $email, 'name' => $name, 'password' => $password]);
        $credentials = ['email' => $email, 'password' => $password];

        if (! $token = JWTAuth::attempt($credentials)) {

            throw AuthException::unauthorized();
        }

        return $token;
    }
}
