<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class SearchUsersAction
{
    public function __construct() {}

    public function handle(string $search): Collection
    {
        $users = User::where('email', 'like', '%'.$search.'%')
            ->orWhere('name', 'like', '%'.$search.'%')
            ->get();

        return $users;
    }
}
