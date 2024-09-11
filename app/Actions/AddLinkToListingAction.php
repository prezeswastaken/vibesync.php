<?php

namespace App\Actions;

use App\Models\Listing;

class AddLinkToListingAction
{
    public function handle(string $title, string $url, string $description, Listing $listing)
    {
        return $listing->links()->create([
            'title' => $title,
            'url' => $url,
            'description' => $description,
        ]);
    }
}
