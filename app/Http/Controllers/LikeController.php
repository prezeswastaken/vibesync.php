<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    public function likeListing(Listing $listing)
    {
        $user = Auth::user();

        if ($user->dislikedListings->contains($listing)) {
            $user->dislikedListings()->detach($listing->id);
        }

        if ($user->likedListings->contains($listing)) {
            $user->likedListings()->detach($listing->id);

            return response()->json(['message' => 'Listing unliked'], 200);
        }

        $user->likedListings()->attach($listing->id);

        return response()->json(['message' => 'Listing liked'], 201);
    }

    public function dislikeListing(Listing $listing)
    {
        $user = Auth::user();

        if ($user->likedListings->contains($listing)) {
            $user->likedListings()->detach($listing->id);
        }

        if ($user->dislikedListings->contains($listing)) {
            $user->dislikedListings()->detach($listing->id);

            return response()->json(['message' => 'Listing undisliked'], 200);
        }

        $user->dislikedListings()->attach($listing->id);

        return response()->json(['message' => 'Listing disliked'], 201);
    }
}
