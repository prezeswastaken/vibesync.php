<?php

namespace App\Actions;

use App\Models\Listing;

class StoreListingAction
{
    public function handle(
        string $title,
        string $body,
        bool $isSaleOffer,
        ?float $price,
        ?int $currencyId,
        array $tagIds,
        array $genreIds,
        int $userId,
    ) {
        $listing = Listing::create([
            'title' => $title,
            'body' => $body,
            'is_sale_offer' => $isSaleOffer,
            'user_id' => $userId,
        ]);

        if ($isSaleOffer) {
            $listing->price()->create([
                'amount' => $price,
                'currency_id' => $currencyId,
            ]);
        }

        $listing->tags()->attach($tagIds);
        $listing->genres()->attach($genreIds);

        return $listing;
    }
}
