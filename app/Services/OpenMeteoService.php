<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenMeteoService
{
    protected string $baseUrl = 'https://api.open-meteo.com/v1';

    public function getCurrentWeather(float $latitude, float $longitude): ?array
    {
        $response = Http::withToken($this->apiKey)
    ->timeout(15)
    ->retry(2, 500, throw: false)  // tambahkan throw: false
    ->get("{$this->baseUrl}/forecast", [
            'latitude' => $latitude,
            'longitude' => $longitude,
            'current' => 'temperature_2m,precipitation,wind_speed_10m',
        ]);

        if ($response->failed()) {
            Log::error('OpenMeteoService: gagal fetch cuaca', [
                'lat' => $latitude, 'lon' => $longitude,
            ]);
            return null;
        }

        $data = $response->json('current');

        return [
            'temperature' => $data['temperature_2m'] ?? null,
            'rainfall'    => $data['precipitation'] ?? null,
            'wind_speed'  => $data['wind_speed_10m'] ?? null,
            'storm_risk'  => $this->calculateStormRisk($data['wind_speed_10m'] ?? 0),
        ];
    }

    protected function calculateStormRisk(float $windSpeed): string
    {
        return match (true) {
            $windSpeed >= 60 => 'high',
            $windSpeed >= 30 => 'medium',
            default => 'low',
        };
    }
    public function getBulkWeather(array $locations): array
{
    // $locations format: [country_id => ['lat' => ..., 'lng' => ...], ...]
    $results = [];

    foreach (array_chunk($locations, 500, true) as $chunk) {
        $latitudes = implode(',', array_column($chunk, 'lat'));
        $longitudes = implode(',', array_column($chunk, 'lng'));

        $response = Http::timeout(30)->retry(2, 500, throw: false)->get("{$this->baseUrl}/forecast", [
            'latitude' => $latitudes,
            'longitude' => $longitudes,
            'current' => 'temperature_2m,precipitation,wind_speed_10m',
        ]);

        if ($response->failed()) {
            Log::error('OpenMeteoService: gagal fetch cuaca massal', [
                'status' => $response->status(),
            ]);
            continue;
        }

        $data = $response->json();
        $countryIds = array_keys($chunk);

        $items = array_key_exists('current', $data) ? [$data] : $data;

        foreach ($items as $i => $item) {
            $countryId = $countryIds[$i] ?? null;
            if (!$countryId) {
                continue;
            }

            $current = $item['current'] ?? [];

            $results[$countryId] = [
                'temperature' => $current['temperature_2m'] ?? null,
                'rainfall'    => $current['precipitation'] ?? null,
                'wind_speed'  => $current['wind_speed_10m'] ?? null,
                'storm_risk'  => $this->calculateStormRisk($current['wind_speed_10m'] ?? 0),
            ];
        }
    }

    return $results;
}
}