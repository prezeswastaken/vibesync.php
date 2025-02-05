<?php

namespace App\Http\Controllers;

use App\Actions\GetUserPaginatedListingsAction;
use App\Actions\SearchUsersAction;
use App\Http\Requests\ShowListingsRequest;
use App\Http\Resources\ListingResource;
use App\Http\Resources\UserResource;
use App\Models\Currency;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function search(Request $request, SearchUsersAction $action): JsonResponse
    {
        $search = $request->q ?? '';
        $users = $action->handle($search);

        return response()->json($users);
    }

    public function listings(User $user, ShowListingsRequest $request, GetUserPaginatedListingsAction $action): JsonResponse
    {
        $currency = Currency::find($request->currency_id);
        $listings = $action->handle($user->id, $currency);

        $response = response()->json(ListingResource::collection($listings)->response()->getData(true));

        return $response;
    }

    public function show(User $user): JsonResponse
    {
        $user = UserResource::make($user);

        return response()->json($user, 200);
    }
}
