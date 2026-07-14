<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RestCountriesService
{
    protected string $baseUrl = 'https://api.restcountries.com/countries/v5';
    protected ?string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.restcountries.key');
    }

    public function getAllCountries(): array
    {
        $allCountries = [];
        $limit = 100;
        $offset = 0;

        do {
            $response = Http::withToken($this->apiKey)
    ->timeout(15)
    ->retry(2, 500, throw: false)
    ->get($this->baseUrl, [
                    'limit' => $limit,
                    'offset' => $offset,
                    'response_fields' => 'names.common,codes.alpha_2,codes.alpha_3,capitals,region,population,currencies,coordinates,flag.url_png',
                ]);

            if ($response->failed()) {
                Log::error('RestCountriesService: gagal fetch data negara', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                break;
            }

            $objects = $response->json('data.objects') ?? [];
            $allCountries = array_merge($allCountries, $objects);

            $more = $response->json('data.meta.more') ?? false;
            $offset += $limit;

        } while ($more);

        return $allCountries;
    }
}