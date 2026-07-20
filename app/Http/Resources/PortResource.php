<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PortResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'unlocode' => $this->unlocode,
            'coordinates' => [
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
            ],
            'country' => $this->whenLoaded('country', fn () => [
                'code' => $this->country->code,
                'name' => $this->country->name,
            ]),
        ];
    }
}