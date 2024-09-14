<?php

namespace App\Actions;

use App\Models\Listing;

class AddLinkToListingAction
{
    public function handle(string $url, Listing $listing)
    {
        return $listing->links()->create([
            'url' => $url,
        ]);
    }
}
