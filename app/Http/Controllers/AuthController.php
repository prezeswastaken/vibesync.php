<?php

namespace App\Http\Controllers;

use App\Actions\RegisterUserAction;
use App\Exceptions\AuthException;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(RegisterRequest $request, RegisterUserAction $action)
    {

        $validated = $request->validated();

        $token = $action->handle(
            $validated['email'],
            $validated['name'],
            $validated['password'],
        );

        return $this->respondWithToken($token);
    }

    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = JWTAuth::attempt($credentials)) {

            throw AuthException::unauthorized();
        }

        return $this->respondWithToken($token);

    }

    public function me(#[CurrentUser] User $user): JWTSubject
    {
        return $user;
    }

    public function logout(): JsonResponse
    {
        Auth::logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function refresh(): JsonResponse
    {
        return $this->respondWithToken(Auth::refresh());
    }

    protected function respondWithToken(string $token): JsonResponse
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60,
            'user' => JWTAuth::user(),
        ]);
    }
}
