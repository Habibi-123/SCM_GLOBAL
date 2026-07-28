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
        try {
            $response = Http::timeout(10)
                ->retry(1, 300, throw: false)
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
        } catch (\Throwable $e) {
            Log::warning('GNewsService exception: ' . $e->getMessage());
            return [];
        }
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