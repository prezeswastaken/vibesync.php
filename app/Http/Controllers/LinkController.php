<?php

namespace App\Http\Controllers;

use App\Actions\AddLinkToListingAction;
use App\Exceptions\ListingException;
use App\Http\Requests\StoreLinkRequest;
use App\Models\Link;
use App\Models\Listing;
use JWTAuth;

class LinkController extends Controller
{
    public function store(StoreLinkRequest $request, Listing $listing, AddLinkToListingAction $action)
    {
        if ($listing->user_id !== JWTAuth::user()->id) {
            throw ListingException::unauthorized();
        }

        $link = $action->handle(
            $request->url,
            $listing,
        );

        return response()->json($link, 201);
    }

    public function delete(Link $link)
    {
        if ($link->listing->user_id !== JWTAuth::user()->id) {
            throw ListingException::unauthorized();
        }

        $link->delete();

        return response()->json(['message' => 'Link deleted'], 204);
    }
}
