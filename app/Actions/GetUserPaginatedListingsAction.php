<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\SortByLikesEnum;
use App\Models\Currency;
use App\Models\User;

class GetUserPaginatedListingsAction
{
    public function __construct(
        protected ConvertListingsToTargetCurrencyAction $convert,
    ) {}

    public function handle(int $userId, ?Currency $currency, SortByLikesEnum $sortByLikes)
    {
        $listings = User::findOrFail($userId)->listings()->with([
            'user:id,avatar_url,name,email',
            'usersWhoLiked',
            'usersWhoDisliked',
            'price.currency',
            'tags',
            'genres:id,name',
            'links',
        ])
            ->published()
            ->withCount('usersWhoLiked')
            ->withCount('usersWhoDisLiked')
            ->when($sortByLikes === SortByLikesEnum::None, function ($q) {
                return $q->orderByDesc('created_at');
            })
            ->when($sortByLikes === SortByLikesEnum::Ascending, function ($q) {
                return $q->orderBy('users_who_liked_count');
            })
            ->when($sortByLikes === SortByLikesEnum::Descending, function ($q) {
                return $q->orderByDesc('users_who_liked_count');
            })
            ->paginate(10);

        if (isset($currency)) {
            $collection = $listings->getCollection();
            $collection = $this->convert->handle($collection, $currency);
            $listings->setCollection($collection);
        }

        return $listings;
    }
}
