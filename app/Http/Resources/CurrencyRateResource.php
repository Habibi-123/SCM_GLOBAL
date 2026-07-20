<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CurrencyRateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'base_currency' => $this->base_currency,
            'target_currency' => $this->target_currency,
            'rate' => $this->rate,
            'fetched_at' => $this->fetched_at?->toIso8601String(),
        ];
    }
}