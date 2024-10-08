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
        float|string|null $price,
        ?int $currencyId,
        array $tagIds,
        array $genreIds,
    ) {
        $listing->update([
            'title' => $title,
            'body' => $body,
            'is_sale_offer' => $isSaleOffer,
        ]);

        if ($isSaleOffer) {
            $listing->price()->update([
                'amount' => $price,
                'currency_id' => $currencyId,
            ]);
        } else {
            $listing->price()->delete();
        }

        $listing->tags()->sync($tagIds);
        $listing->genres()->sync($genreIds);

        return $listing;
    }
}
