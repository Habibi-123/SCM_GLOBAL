<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExchangeRateService
{
    protected string $baseUrl = 'https://open.er-api.com/v6';

    public function getRates(string $baseCurrency): array
{
    $response = Http::timeout(15)
        ->retry(2, 500, throw: false)
        ->get("{$this->baseUrl}/latest/{$baseCurrency}");

    if ($response->failed()) {
        Log::error('ExchangeRateService: gagal fetch kurs', [
            'base' => $baseCurrency,
        ]);
        return [];
    }

    return $response->json('rates') ?? [];
    }
}