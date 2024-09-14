<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LinkResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'url' => $this->url,
            'listing_id' => $this->listing_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
