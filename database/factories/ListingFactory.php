<?php

namespace Database\Factories;

use App\Models\Link;
use App\Models\Price;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Listing>
 */
class ListingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence,
            'body' => $this->faker->realTextBetween(10, 1000),
            'is_sale_offer' => $this->faker->boolean,
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (\App\Models\Listing $listing) {
            $listing->genres()->attach([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);
            $listing->tags()->attach([2, 3, 4]);
            Link::factory()->count(3)->create([
                'listing_id' => $listing->id,
            ]);
            Price::factory()->for($listing)->create();
        });

    }

    public function published(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'is_published' => true,
            ];
        });
    }
}
