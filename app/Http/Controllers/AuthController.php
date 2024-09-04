<?php

namespace App\Http\Controllers;

use App\Actions\RegisterUserAction;
use App\Http\Requests\RegisterRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(RegisterRequest $request, RegisterUserAction $action): Response
    {

        $validated = $request->validated();

        $token = $action->handle(
            $validated['email'],
            $validated['name'],
            $validated['password'],
        );

        return $this->respondWithToken($token);
    }

    public function login(): Response
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response(['error' => "Unauthorized! Those credentials didn't match our records."], 401);
        }

        return $this->respondWithToken($token);
    }

    public function me(): JWTSubject
    {
        $user = JWTAuth::user();

        return $user;
    }

    public function logout(): JsonResponse
    {
        auth()->logout();

        return response(['message' => 'Successfully logged out']);
    }

    public function refresh(): Response
    {
        return $this->respondWithToken(auth()->refresh());
    }

    protected function respondWithToken(string $token): Response
    {
        return response([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
        ]);
    }
}
