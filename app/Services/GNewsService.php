<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GNewsService
{
    protected string $baseUrl = 'https://gnews.io/api/v4/search';
    protected ?string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.gnews.key');
    }

    public function search(string $query, int $max = 10): array
    {
        $response = Http::timeout(15)
            ->retry(2, 500, throw: false)
            ->get($this->baseUrl, [
                'q' => $query,
                'lang' => 'en',
                'max' => $max,
                'sortby' => 'publishedAt',
                'apikey' => $this->apiKey,
            ]);

        if ($response->failed()) {
            Log::error('GNewsService: gagal fetch berita', [
                'query' => $query,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [];
        }

        return $response->json('articles') ?? [];
    }

    /**
     * Mencari berita berdasarkan nama negara
     * dengan konteks trade, economy, dan shipping
     * agar hasil lebih relevan untuk supply chain.
     */
    public function searchByCountry(string $countryName, int $max = 5): array
    {
            $query = "\"{$countryName}\"";
            
            return $this->search($query, $max);
    }
}