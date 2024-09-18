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
            'user_id' => $this->user_id,
            'title' => $this->title,
            'author_avatar_url' => $this->user->avatar_url,
            'body' => $this->body,
            'is_sale_offer' => $this->is_sale_offer,
            'price' => $this->is_sale_offer ? PriceResource::make($this->price) : null,
            'is_published' => $this->is_published,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'genres' => $this->genres->pluck('name'),
            'tags' => $this->tags->pluck('name'),
            'links' => LinkResource::collection($this->links),
            'like_count' => $this->usersWhoLiked->count(),
            'dislike_count' => $this->usersWhoDisliked->count(),
            'does_current_user_like' => $this->doesCurrentUserLike(),
            'does_current_user_dislike' => $this->doesCurrentUserDislike(),
        ];
    }
}
