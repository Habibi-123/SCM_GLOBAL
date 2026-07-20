<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RiskScoreResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'weather_score' => $this->weather_score,
            'inflation_score' => $this->inflation_score,
            'exchange_score' => $this->exchange_score,
            'news_score' => $this->news_score,
            'total_score' => $this->total_score,
            'risk_level' => $this->risk_level,
            'calculated_at' => $this->calculated_at?->toIso8601String(),
        ];
    }
}