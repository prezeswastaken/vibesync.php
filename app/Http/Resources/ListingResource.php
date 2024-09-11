<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ListingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'author' => $this->user->name,
            'title' => $this->title,
            'body' => $this->body,
            'price' => $this->price,
            'is_sale_offer' => $this->is_sale_offer,
            'is_published' => $this->is_published,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'genres' => $this->genres->pluck('name'),
            'tags' => $this->tags->pluck('name'),
            'links' => $this->links,
        ];
    }
}
