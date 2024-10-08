<?php

namespace App\Http\Controllers;

use App\Events\ListingLiked;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;

class LikeController extends Controller
{
    public function likeListing(Listing $listing, #[CurrentUser] User $user)
    {
        if ($user->dislikedListings->contains($listing)) {
            $user->dislikedListings()->detach($listing->id);
        }

        if ($user->likedListings->contains($listing)) {
            $user->likedListings()->detach($listing->id);

            return response()->json(['message' => 'Listing unliked'], 200);
        }

        $user->likedListings()->attach($listing->id);

        broadcast(new ListingLiked($listing, $user->id, $user->name))->toOthers();

        return response()->json(['message' => 'Listing liked'], 201);
    }

    public function dislikeListing(Listing $listing, #[CurrentUser] User $user)
    {
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
