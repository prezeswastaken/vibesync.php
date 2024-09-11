<?php

namespace App\Http\Controllers;

use App\Actions\SetAvatarForUserAction;
use App\Http\Requests\StoreAvatarRequest;
use Auth;

class AvatarController extends Controller
{
    public function store(StoreAvatarRequest $request, SetAvatarForUserAction $action)
    {
        $image = $request->file('avatar');

        $action->handle(Auth::user(), $image);

        return response()->json(['message' => 'Avatar changed succesfully!'], 201);
    }
}
