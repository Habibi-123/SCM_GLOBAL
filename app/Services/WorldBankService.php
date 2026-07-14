<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WorldBankService
{
    protected string $baseUrl = 'https://api.worldbank.org/v2';

    public function getIndicator(string $countryCode, string $indicatorCode, int $year): ?float
    {
        $response = Http::withToken($this->apiKey)
    ->timeout(15)
    ->retry(2, 500, throw: false)
    ->get(
            "{$this->baseUrl}/country/{$countryCode}/indicator/{$indicatorCode}",
            [
                'format' => 'json',
                'date' => $year,
            ]
        );

        if ($response->failed()) {
            Log::error('WorldBankService: gagal fetch indikator', [
                'country' => $countryCode, 'indicator' => $indicatorCode,
            ]);
            return null;
        }

        $data = $response->json()[1] ?? [];

        return $data[0]['value'] ?? null;
    }

    public function getGdp(string $countryCode, int $year): ?float
    {
        return $this->getIndicator($countryCode, 'NY.GDP.MKTP.CD', $year);
    }

    public function getInflation(string $countryCode, int $year): ?float
    {
        return $this->getIndicator($countryCode, 'FP.CPI.TOTL.ZG', $year);
    }

    public function getExports(string $countryCode, int $year): ?float
    {
        return $this->getIndicator($countryCode, 'NE.EXP.GNFS.CD', $year);
    }

    public function getImports(string $countryCode, int $year): ?float
    {
        return $this->getIndicator($countryCode, 'NE.IMP.GNFS.CD', $year);
    }
    public function getIndicatorForAllCountries(string $indicatorCode, int $year): array
{
    $response = Http::timeout(20)->retry(2, 500, throw: false)->get(
        "{$this->baseUrl}/country/all/indicator/{$indicatorCode}",
        [
            'format' => 'json',
            'date' => $year,
            'per_page' => 300,
        ]
    );

    if ($response->failed()) {
        Log::error('WorldBankService: gagal fetch indikator massal', [
            'indicator' => $indicatorCode,
            'status' => $response->status(),
        ]);
        return [];
    }

    $data = $response->json()[1] ?? [];

    $result = [];
    foreach ($data as $row) {
        $code = $row['countryiso3code'] ?? null;
        if ($code && $row['value'] !== null) {
            $result[$code] = $row['value'];
        }
    }

    return $result;
}
}