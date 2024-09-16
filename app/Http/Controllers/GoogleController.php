<?php

namespace App\Http\Controllers;

use App\Exceptions\SocialiteException;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use JWTAuth;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function auth(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback(): RedirectResponse
    {
        $googleUser = Socialite::driver('google')->user();

        $user = User::where('email', $googleUser->email)->first();

        if ($user !== null && $user->google_id === null) {
            throw SocialiteException::emailTaken();
        } elseif ($user !== null && $user->google_id !== null) {
            $jwtToken = JWTAuth::fromUser($user);

            return $this->redirectToFrontend($jwtToken);
        }

        $user = User::create([
            'name' => $googleUser->name,
            'email' => $googleUser->email,
            'avatar_url' => $googleUser->user['picture'] ?? null,
            'google_id' => $googleUser->id,
        ]);

        $jwtToken = JWTAuth::fromUser($user);

        return $this->redirectToFrontend($jwtToken);
    }

    private function redirectToFrontend(string $jwtToken): RedirectResponse
    {
        $frontendUrl = config('frontend.url');

        return redirect("$frontendUrl/login?token=$jwtToken");
    }
}
