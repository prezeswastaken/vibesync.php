<?php

namespace App\Http\Controllers;

use App\Actions\AddLinkToListingAction;
use App\Actions\StoreListingAction;
use App\Exceptions\ListingException;
use App\Http\Requests\StoreLinkRequest;
use App\Http\Requests\StoreListingRequest;
use App\Http\Resources\ListingResource;
use App\Models\Listing;
use JWTAuth;

class ListingController extends Controller
{
    public function index()
    {
        return response()->json(ListingResource::collection(Listing::with('user')->where('is_published', true)->get()));
    }

    public function store(StoreListingRequest $request, StoreListingAction $action)
    {
        $validated = $request->validated();

        $listing = $action->handle(
            $validated['title'],
            $validated['body'],
            $validated['is_sale_offer'],
            $validated['price'],
            $validated['tag_ids'],
            $validated['genre_ids'],
            JWTAuth::user()->id,
        );

        return response()->json($listing, 201);
    }

    public function show(Listing $listing)
    {
        $listing->load('tags', 'genres', 'links');

        return response()->json($listing);
    }

    public function addLink(StoreLinkRequest $request, Listing $listing, AddLinkToListingAction $action)
    {
        if ($listing->user_id !== JWTAuth::user()->id) {
            throw ListingException::unauthorized();
        }

        $link = $action->handle(
            $request->title,
            $request->url,
            $request->description,
            $listing,
        );

        return response()->json($link, 201);
    }

    public function publish(Listing $listing)
    {
        if ($listing->user_id !== JWTAuth::user()->id) {
            throw ListingException::unauthorized();
        }

        $listing->is_published = true;
        $listing->save();

        return response()->json($listing);
    }

    public function unpublish(Listing $listing)
    {
        if ($listing->user_id !== JWTAuth::user()->id) {
            throw ListingException::unauthorized();
        }

        $listing->is_published = false;
        $listing->save();

        return response()->json($listing);
    }
}
