<?php

namespace App\DTOs;

use App\Models\Currency;

class CurrencyDTO extends DTO
{
    private function __construct(
        protected string $code,
        protected float $rateToUSD,
    ) {}

    public static function fromCurrency(Currency $currency): self
    {
        return new self(
            code: $currency->code,
            rateToUSD: $currency->rate_to_usd,
        );
    }

    public static function fromId(int $id): self
    {
        $currency = Currency::findOrFail($id);

        return self::fromCurrency($currency);
    }

    public static function USD(): self
    {
        return new self(
            code: 'USD',
            rateToUSD: 1.0,
        );
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getRateToUSD(): float
    {
        return $this->rateToUSD;
    }
}
