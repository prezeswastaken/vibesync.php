<?php

namespace App\Http\Controllers;

use App\Actions\StoreListingAction;
use App\Http\Requests\StoreListingRequest;
use App\Http\Requests\UpdateListingRequest;
use App\Http\Resources\ListingResource;
use App\Models\Listing;
use JWTAuth;

class ListingController extends Controller
{
    public function index()
    {
        return response()->json(ListingResource::collection(Listing::all()));
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

    /**
     * Display the specified resource.
     */
    public function show(Listing $listing)
    {
        $listing->load('tags', 'genres', 'links');

        return response()->json($listing);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Listing $listing)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateListingRequest $request, Listing $listing)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Listing $listing)
    {
        //
    }
}
