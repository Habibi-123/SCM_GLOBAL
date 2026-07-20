<?php

namespace App\Services;

use App\Models\Country;
use App\Models\NewsArticle;
use Illuminate\Support\Facades\Cache;

class CountryNewsService
{
    protected int $cacheHours = 24;

    public function __construct(
        protected GNewsService $gNewsService,
        protected SentimentAnalysisService $sentimentService,
    ) {}

    public function getNewsForCountry(Country $country): \Illuminate\Support\Collection
    {
        $cacheKey = "news_fetched:{$country->code}";

        // Kalau BELUM pernah di-cek dalam 24 jam terakhir, fetch baru dari API
        if (!Cache::has($cacheKey)) {
            $this->fetchAndStore($country);

            // Tandai sudah di-cek, supaya TIDAK fetch ulang lagi dalam 24 jam
            // ke depan walau hasilnya 0 berita (mencegah request percuma berulang)
            Cache::put($cacheKey, true, now()->addHours($this->cacheHours));
        }

        return NewsArticle::where('country_id', $country->id)
            ->orderByDesc('published_at')
            ->limit(10)
            ->get();
    }

    public function refreshNewsForCountry(Country $country): void
    {
        $cacheKey = "news_fetched:{$country->code}";

        // Hapus cache lama, paksa fetch baru
        Cache::forget($cacheKey);

        $this->fetchAndStore($country);

        Cache::put($cacheKey, true, now()->addHours($this->cacheHours));
    }

    protected function fetchAndStore(Country $country): void
    {
        $articles = $this->gNewsService->searchByCountry($country->name);

        foreach ($articles as $item) {
            $sentiment = $this->sentimentService->analyze($item['title'] ?? '');

            NewsArticle::updateOrCreate(
                ['url' => $item['url']],
                [
                    'country_id' => $country->id,
                    'title' => $item['title'] ?? '-',
                    'source' => $item['source']['name'] ?? null,
                    'category' => 'country-specific',
                    'positive_count' => $sentiment['positive_count'],
                    'negative_count' => $sentiment['negative_count'],
                    'sentiment' => $sentiment['sentiment'],
                    'published_at' => $item['publishedAt'] ?? null,
                ]
            );
        }
    }
}