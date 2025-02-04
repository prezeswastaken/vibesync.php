<?php

namespace App\Http\Controllers;

use App\Exceptions\UpdateAccountException;
use App\Http\Requests\UpdateAccountRequest;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;

class AccountController extends Controller
{
    public function update(UpdateAccountRequest $request, #[CurrentUser] User $user)
    {
        if (isset($request->password) && ! password_verify($request->current_password, $user->password)) {
            throw UpdateAccountException::invalidPassword();
        }

        if (isset($request->email) && $user->email != $request->current_email) {
            throw UpdateAccountException::wrongCurrentEmail();
        }

        $user = tap($user)->update($request->only(['name', 'password', 'email']));

        return response()->json($user);
    }
}
