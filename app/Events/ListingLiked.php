<?php

namespace App\Events;

use App\Http\Resources\ListingResource;
use App\Models\Listing;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ListingLiked implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $listing;

    public function __construct(
        Listing $listing,
        protected int $userID,
        public string $nameOfUserWhoLiked,
    ) {
        $this->listing = ListingResource::make($listing);
    }

    /**
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel("user.{$this->listing->user_id}"),
        ];
    }
}
