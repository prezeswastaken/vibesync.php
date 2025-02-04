<?php

namespace App\Http\Controllers;

use App\Actions\SearchUsersAction;
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
}
