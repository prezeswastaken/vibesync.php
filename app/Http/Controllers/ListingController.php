<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\StoreListingAction;
use App\Actions\UpdateListingAction;
use App\Exceptions\ListingException;
use App\Http\Requests\StoreListingRequest;
use App\Http\Requests\UpdateListingRequest;
use App\Http\Resources\ListingResource;
use App\Http\Resources\ShowListingResource;
use App\Models\Listing;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use JWTAuth;

class ListingController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = 10;

        $listings = Listing::with('user:avatar_url,name,id', 'usersWhoLiked', 'usersWhoDisliked')
            ->where('is_published', true)
            ->orderByDesc('created_at')
            ->paginate($perPage);

        return response()->json(ListingResource::collection($listings)->response()->getData(true));
    }

    public function myIndex(): JsonResponse
    {
        $user = Auth::user();

        $perPage = request()->input('per_page', 10);

        $listings = $user->listings()
            ->with('user:avatar_url,name,id', 'usersWhoLiked', 'usersWhoDisliked')
            ->orderByDesc('created_at')
            ->paginate($perPage);

        return response()->json(ListingResource::collection($listings)->response()->getData(true));
    }

    public function store(StoreListingRequest $request, StoreListingAction $action): JsonResponse
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

    public function show(Listing $listing): JsonResponse
    {
        if (! $listing->is_published && $listing->user_id !== JWTAuth::user()->id) {
            throw ListingException::notFound();
        }

        return response()->json(new ShowListingResource($listing));
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
            $request->tag_ids,
            $request->genre_ids,
        );

        return response()->json($listing);
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
