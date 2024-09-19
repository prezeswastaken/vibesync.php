<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Models\Currency;
use App\Models\Price;

class MoneyDTO extends DTO
{
    private function __construct(
        protected float $amount,
        protected CurrencyDTO $currency,
    ) {}

    public static function make(float $amount, CurrencyDTO $currencyDTO)
    {
        return new self($amount, $currencyDTO);
    }

    public static function fromPrice(Price $price): ?self
    {
        if ($price === null) {
            return null;
        }

        return new self(
            amount: $price->amount,
            currency: CurrencyDTO::fromCurrency($price->currency),
        );
    }

    public static function fromAmountAndCurrency(float $amount, Currency $currency): self
    {
        return new self($amount, CurrencyDTO::fromCurrency($currency));
    }

    private function toUSD(): self
    {
        $newAmount = $this->amount * $this->currency->getRateToUSD();
        $newCurrency = CurrencyDTO::USD();

        return self::make($newAmount, $newCurrency);
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getCode(): string
    {
        return $this->currency->getCode();
    }

    public function getCurrency(): CurrencyDTO
    {
        return $this->currency;
    }

    public function convertTo(int $currencyId): self
    {
        $thisInUSD = $this->toUSD();
        $newCurrency = CurrencyDTO::fromId($currencyId);

        $newAmount = $thisInUSD->amount / $newCurrency->getRateToUSD();
        $newCurrency = $newCurrency;

        return $this::make($newAmount, $newCurrency);
    }
}
