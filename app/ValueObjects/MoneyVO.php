<?php

namespace App\ValueObjects;

use App\Models\Currency;
use App\Models\Price;

class MoneyVO
{
    private function __construct(
        protected float $amount,
        protected CurrencyVO $currency,
    ) {}

    public static function fromPrice(Price $price): ?self
    {
        if ($price === null) {
            return null;
        }

        return new self(
            amount: $price->amount,
            currency: CurrencyVO::fromCurrency($price->currency),
        );
    }

    public static function fromAmountAndCurrency(float $amount, Currency $currency): self
    {
        return new self($amount, CurrencyVO::fromCurrency($currency));
    }

    private function toUSD(): self
    {
        $this->amount = $this->amount * $this->currency->getRateToUSD();
        $this->currency = CurrencyVO::USD();

        return $this;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getCode(): string
    {
        return $this->currency->getCode();
    }

    public function convertTo(int $currencyId): self
    {
        $thisInUSD = $this->toUSD();
        $newCurrency = CurrencyVO::fromId($currencyId);

        $this->amount = $thisInUSD->amount / $newCurrency->getRateToUSD();
        $this->currency = $newCurrency;

        return $this;
    }
}
