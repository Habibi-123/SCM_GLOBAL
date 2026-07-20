<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EconomicIndicatorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'year' => $this->year,
            'gdp' => $this->gdp,
            'inflation' => $this->inflation,
            'exports' => $this->exports,
            'imports' => $this->imports,
        ];
    }
}