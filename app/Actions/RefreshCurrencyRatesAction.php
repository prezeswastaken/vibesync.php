<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Currency;
use Illuminate\Support\Facades\Http;

class RefreshCurrencyRatesAction
{
    public function __construct() {}

    public function handle(): void
    {
        $key = config('services.exchange_rate_api.key');
        $url = "https://v6.exchangerate-api.com/v6/$key/latest/USD";

        $reponse = Http::get($url);

        $rates = $reponse->json()['conversion_rates'];

        $currencies = Currency::all();

        $currencies->each(function ($currency) use ($rates) {
            $rate = floatval($rates[$currency->code]);

            if ($rate == 0) {
                return;
            }

            $currency->rate_to_usd = 1 / $rate;
            $currency->save();
        });

    }
}
