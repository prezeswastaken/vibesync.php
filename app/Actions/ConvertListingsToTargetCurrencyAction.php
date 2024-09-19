<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Currency;
use Illuminate\Database\Eloquent\Collection;

class ConvertListingsToTargetCurrencyAction
{
    public function __construct(
        protected ConvertListingAction $convert,
    ) {}

    public function handle(Collection $listings, Currency $currency): Collection
    {
        $newListings = $listings->map(function ($listing) use ($currency) {
            $newListing = $this->convert->handle($listing, $currency);

            return $newListing;
        });

        return $newListings;
    }
}
