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
        array $tagIds,
        array $genreIds,
        int $userId,
    ) {
        $listing = Listing::create([
            'title' => $title,
            'body' => $body,
            'is_sale_offer' => $isSaleOffer,
            'price' => $price,
            'user_id' => $userId,
        ]);

        $listing->tags()->attach($tagIds);
        $listing->genres()->attach($genreIds);

        return $listing;
    }
}
