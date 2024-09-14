<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShowListingResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'author' => $this->user->name,
            'user_id' => $this->user_id,
            'title' => $this->title,
            'author_avatar_url' => $this->user->avatar_url,
            'body' => $this->body,
            'price' => $this->price,
            'is_sale_offer' => $this->is_sale_offer,
            'is_published' => $this->is_published,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'genres' => GenreResource::collection($this->genres),
            'tags' => TagResource::collection($this->tags),
            'links' => LinkResource::collection($this->links),
        ];
    }
}
