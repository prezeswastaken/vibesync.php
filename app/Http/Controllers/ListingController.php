<?php

namespace App\Http\Controllers;

use App\Actions\AddLinkToListingAction;
use App\Actions\StoreListingAction;
use App\Actions\UpdateListingAction;
use App\Exceptions\ListingException;
use App\Http\Requests\StoreLinkRequest;
use App\Http\Requests\StoreListingRequest;
use App\Http\Requests\UpdateListingRequest;
use App\Http\Resources\ListingResource;
use App\Models\Listing;
use JWTAuth;

class ListingController extends Controller
{
    public function index()
    {
        return response()->json(ListingResource::collection(
            Listing::with('user:avatar_url,name,id')
                ->where('is_published', true)
                ->orderByDesc('created_at')
                ->get()
        ));
    }

    public function myIndex()
    {
        $user = JWTAuth::user();

        return response()->json(ListingResource::collection(
            $user->listings
                ->load('user:avatar_url,name,id')->sortByDesc('created_at')
        ));
    }

    public function store(StoreListingRequest $request, StoreListingAction $action)
    {
        $listing = $action->handle(
            $request->title,
            $request->body,
            $request->is_sale_offer,
            $request->price,
            $request->tag_ids,
            $request->genre_ids,
            JWTAuth::user()->id,
        );

        return response()->json($listing, 201);
    }

    public function show(Listing $listing)
    {
        if (! $listing->is_published && $listing->user_id !== JWTAuth::user()->id) {
            throw ListingException::notFound();
        }

        return response()->json(new ListingResource($listing));
    }

    public function update(Listing $listing, UpdateListingRequest $request, UpdateListingAction $action)
    {
        if ($listing->user_id !== JWTAuth::user()->id) {
            throw ListingException::unauthorized();
        }

        $listing = $action->handle(
            $listing,
            $request->title,
            $request->body,
            $request->is_sale_offer,
            $request->price,
            $request->tag_ids,
            $request->genre_ids,
        );

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
