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
        $listings = Listing::orderByDesc('created_at')->paginate(10);

        if ($currency != null) {
            $collection = $listings->getCollection();
            $collection = $this->convert->handle($collection, $currency);
            $listings->setCollection($collection);
        }

        return $listings;
    }
}
