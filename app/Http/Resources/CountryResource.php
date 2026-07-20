<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CountryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'code' => $this->code,
            'name' => $this->name,
            'region' => $this->region,
            'capital' => $this->capital,
            'population' => $this->population,
            'currency_code' => $this->currency_code,
            'flag_url' => $this->flag_url,
            'coordinates' => [
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
            ],
            // Relasi ini cuma dimuat kalau di-eager load dari Controller (whenLoaded),
            // supaya endpoint /api/countries (list) tetap ringan tanpa data ini
            'latest_economic_indicator' => new EconomicIndicatorResource($this->whenLoaded('latestEconomicIndicator')),
            'latest_risk_score' => new RiskScoreResource($this->whenLoaded('latestRiskScore')),
        ];
    }
}