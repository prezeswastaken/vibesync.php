<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;

class AuthOnly extends Middleware
{
    /**
     * We do this to ensure that even when application/json isn't passed, we still return a JSON response
     */
    protected function redirectTo(Request $request): ?string
    {
        throw new HttpResponseException(response(['message' => 'Unauthenticated.'], 401));
    }
}
