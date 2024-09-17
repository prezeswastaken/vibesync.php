<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencies = [
            ['code' => 'USD', 'rate_to_usd' => 1.0],
            ['code' => 'EUR', 'rate_to_usd' => 1.2],
            ['code' => 'PLN', 'rate_to_usd' => 0.25],
        ];

        foreach ($currencies as $currency) {
            \App\Models\Currency::create($currency);
        }
    }
}
