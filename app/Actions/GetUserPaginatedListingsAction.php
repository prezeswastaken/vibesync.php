<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Currency;
use App\Models\User;

class GetUserPaginatedListingsAction
{
    public function __construct(
        protected ConvertListingsToTargetCurrencyAction $convert,
    ) {}

    public function handle(int $userId, ?Currency $currency = null)
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
            ->orderByDesc('created_at')
            ->paginate(10);

        if (isset($currency)) {
            $collection = $listings->getCollection();
            $collection = $this->convert->handle($collection, $currency);
            $listings->setCollection($collection);
        }

        return $listings;
    }
}
