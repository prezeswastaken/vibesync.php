<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\DTOs\MoneyDTO;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PriceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $currencyId = intval($request->currency_id);
        $price = $this->getPriceInTargetCurrency($currencyId);

        return [
            'id' => $this->id,
            'amount' => $price->getAmount(),
            'currency_code' => $price->getCode(),
        ];
    }

    private function getPriceInTargetCurrency(int $targetCurrencyId): MoneyDTO
    {
        $price = MoneyDTO::fromAmountAndCurrency($this->amount, $this->currency);

        if ($targetCurrencyId !== 0 && $targetCurrencyId !== $this->currency->id) {
            $price = $price->convertTo($targetCurrencyId);
        }

        return $price;
    }
}
