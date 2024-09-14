<?php

namespace App\Http\Controllers;

use App\Models\User;
use JWTAuth;
use Laravel\Socialite\Facades\Socialite;

class SpotifyController extends Controller
{
    public function auth()
    {
        return Socialite::driver('spotify')->redirect();
    }

    public function callback()
    {
        $spotifyUser = Socialite::driver('spotify')->user();

        $user = User::where('spotify_id', $spotifyUser->id)->first();

        if ($user !== null) {
            $jwtToken = JWTAuth::fromUser($user);

            return $this->redirectToFrontend($jwtToken);
        }

        $user = User::create([
            'name' => $spotifyUser->name,
            'email' => $spotifyUser->email,
            'avatar_url' => $spotifyUser->avatar ?? null,
            'spotify_id' => $spotifyUser->id,
        ]);

        $jwtToken = JWTAuth::fromUser($user);

        return $this->redirectToFrontend($jwtToken);
    }

    private function redirectToFrontend(string $jwtToken)
    {
        $frontendUrl = config('frontend.url');

        return redirect("$frontendUrl/login?token=$jwtToken");
    }
}
