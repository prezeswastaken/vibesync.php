<?php

declare(strict_types=1);

namespace App\Actions;

use App\DTOs\MoneyDTO;
use App\Models\Currency;
use App\Models\Listing;

class ConvertListingAction
{
    /**
     * Create a new class instance.
     */
    public function __construct() {}

    public function handle(Listing $listing, ?Currency $currency = null): Listing
    {
        $newPrice = $listing->price;
        if ($newPrice == null) {
            return $listing;
        }

        if ($currency == null) {
            return $listing;
        }

        $priceDTO = MoneyDTO::fromPrice($listing->price);
        $newPriceDTO = $priceDTO->convertTo($currency);

        $newPrice->amount = $newPriceDTO->getAmount();
        $newPrice->currency_id = $currency->id;
        $newPrice->currency_code = $currency->code;

        return $listing;
    }
}
