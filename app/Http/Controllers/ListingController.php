<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\ConvertListingAction;
use App\Actions\GetMyPaginatedListingsAction;
use App\Actions\GetPaginatedListingsAction;
use App\Actions\StoreListingAction;
use App\Actions\UpdateListingAction;
use App\Exceptions\ListingException;
use App\Http\Requests\ShowListingsRequest;
use App\Http\Requests\StoreListingRequest;
use App\Http\Requests\UpdateListingRequest;
use App\Http\Resources\ListingResource;
use App\Http\Resources\ShowListingResource;
use App\Models\Currency;
use App\Models\Listing;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use JWTAuth;

class ListingController extends Controller
{
    public function index(ShowListingsRequest $request, GetPaginatedListingsAction $action): JsonResponse
    {
        $currency = Currency::find($request->currency_id);
        $listings = $action->handle($currency);

        return response()->json(ListingResource::collection($listings)->response()->getData(true));
    }

    public function myIndex(ShowListingsRequest $request, GetMyPaginatedListingsAction $action): JsonResponse
    {
        $currency = Currency::find($request->currency_id);
        $listings = $action->handle($currency);

        return response()->json(ListingResource::collection($listings)->response()->getData(true));
    }

    public function store(StoreListingRequest $request, StoreListingAction $action): JsonResponse
    {
        $listing = $action->handle(
            $request->title,
            $request->body,
            $request->is_sale_offer,
            $request->price,
            $request->currency_id,
            $request->tag_ids,
            $request->genre_ids,
            JWTAuth::user()->id,
        );

        return response()->json(ShowListingResource::make($listing), 201);
    }

    public function show(Listing $listing, Request $request, ConvertListingAction $convert): JsonResponse
    {
        $request->validate([
            'currency_id' => 'nullable|exists:currencies,id',
        ]);

        if (! $listing->is_published && $listing->user_id !== JWTAuth::user()->id) {
            throw ListingException::notFound();
        }

        $currency = Currency::find($request->currency_id);
        $listing = $convert->handle($listing, $currency);

        return response()->json(ShowListingResource::make($listing));
    }

    public function update(Listing $listing, UpdateListingRequest $request, UpdateListingAction $action): JsonResponse
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
            $request->currency_id,
            $request->tag_ids,
            $request->genre_ids,
        );

        return response()->json(ShowListingResource::make($listing));
    }

    public function publish(Listing $listing): JsonResponse
    {
        if ($listing->user_id !== JWTAuth::user()->id) {
            throw ListingException::unauthorized();
        }

        $listing->is_published = true;
        $listing->save();

        return response()->json($listing);
    }

    public function unpublish(Listing $listing): JsonResponse
    {
        if ($listing->user_id !== JWTAuth::user()->id) {
            throw ListingException::unauthorized();
        }

        $listing->is_published = false;
        $listing->save();

        return response()->json($listing);
    }

    public function destroy(Listing $listing): JsonResponse
    {
        if ($listing->user_id !== JWTAuth::user()->id) {
            throw ListingException::unauthorized();
        }

        $listing->delete();

        return response()->json(['message' => 'Listing deleted succesfully!'], 204);
    }
}
