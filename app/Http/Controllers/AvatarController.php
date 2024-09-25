<?php

namespace App\Http\Controllers;

use App\Actions\SetAvatarForUserAction;
use App\Http\Requests\StoreAvatarRequest;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\JsonResponse;

class AvatarController extends Controller
{
    public function store(
        StoreAvatarRequest $request,
        SetAvatarForUserAction $action,
        #[CurrentUser] User $user,
    ): JsonResponse {
        $image = $request->file('avatar');

        $action->handle($user, $image);

        return response()->json(['message' => 'Avatar changed succesfully!'], 201);
    }
}
