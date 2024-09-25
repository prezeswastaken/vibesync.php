<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Currency;
use App\Models\Listing;

class GetPaginatedListingsAction
{
    public function __construct(
        protected ConvertListingsToTargetCurrencyAction $convert,
    ) {}

    public function handle(?Currency $currency = null)
    {
        $listings = Listing::with(['user:id,avatar_url,name', 'usersWhoLiked', 'usersWhoDisliked', 'price.currency', 'tags', 'genres:id,name', 'links'])->published()->orderByDesc('created_at')->paginate(10);

        if (isset($currency)) {
            $collection = $listings->getCollection();
            $collection = $this->convert->handle($collection, $currency);
            $listings->setCollection($collection);
        }

        return $listings;
    }
}
