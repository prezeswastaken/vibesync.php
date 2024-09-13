<?php

namespace App\Actions;

use App\Models\Listing;

class UpdateListingAction
{
    public function handle(
        Listing $listing,
        string $title,
        string $body,
        bool $isSaleOffer,
        ?float $price,
        array $tagIds,
        array $genreIds,
    ) {
        $listing->update([
            'title' => $title,
            'body' => $body,
            'is_sale_offer' => $isSaleOffer,
            'price' => $price,
        ]);

        $listing->tags()->sync($tagIds);
        $listing->genres()->sync($genreIds);

        return $listing;
    }
}
