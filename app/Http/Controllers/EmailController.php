<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Exceptions\EmailException;
use App\Http\Requests\SetEmailRequest;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\JsonResponse;

class EmailController extends Controller
{
    public function update(SetEmailRequest $request, #[CurrentUser] User $user): JsonResponse
    {
        if (isset($user->email)) {
            throw EmailException::userAlreadyHasEmail();
        }

        $user = tap($user)->update($request->only(['email']));

        return response()->json($user, 200);
    }
}
