<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Price>
 */
class PriceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $currencyId = \App\Models\Currency::inRandomOrder()->first()->id;

        return [
            'amount' => $this->faker->randomFloat(2, 1, 1000),
            'currency_id' => $currencyId,
        ];
    }
}
