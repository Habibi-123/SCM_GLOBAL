<?php

namespace App\Services;

use App\Models\Country;
use App\Models\NewsArticle;

class RiskScoringService
{
    // Bobot sesuai spesifikasi project
    protected float $weatherWeight = 0.30;
    protected float $inflationWeight = 0.20;
    protected float $newsWeight = 0.40;
    protected float $currencyWeight = 0.10;

    public function calculateForCountry(Country $country): array
    {
        $weatherScore = $this->scoreWeather($country);
        $inflationScore = $this->scoreInflation($country);
        $newsScore = $this->scoreNews($country);
        $currencyScore = $this->scoreCurrency();

        $totalScore = ($weatherScore * $this->weatherWeight)
            + ($inflationScore * $this->inflationWeight)
            + ($newsScore * $this->newsWeight)
            + ($currencyScore * $this->currencyWeight);

        $riskLevel = match (true) {
            $totalScore >= 60 => 'high',
            $totalScore >= 35 => 'medium',
            default => 'low',
        };

        return [
            'weather_score' => round($weatherScore, 2),
            'inflation_score' => round($inflationScore, 2),
            'exchange_score' => round($currencyScore, 2),
            'news_score' => round($newsScore, 2),
            'total_score' => round($totalScore, 2),
            'risk_level' => $riskLevel,
        ];
    }

    protected function scoreWeather(Country $country): float
    {
        $latest = $country->weatherData()->latest('fetched_at')->first();

        if (!$latest) {
            return 50;
        }

        return match ($latest->storm_risk) {
            'high' => 90,
            'medium' => 50,
            default => 10,
        };
    }

    protected function scoreInflation(Country $country): float
    {
        $latest = $country->economicIndicators()->latest('year')->first();

        if (!$latest || is_null($latest->inflation)) {
            return 50;
        }

        $inflationAbs = abs((float) $latest->inflation);
        $score = ($inflationAbs / 20) * 100;

        return min(100, max(0, $score));
    }

    /**
     * Hitung skor berita berdasarkan berita spesifik negara.
     * Jika belum ada, gunakan berita global sebagai fallback.
     */
    protected function scoreNews(Country $country): float
    {
        $countryNewsQuery = NewsArticle::where('country_id', $country->id)
            ->whereNotNull('sentiment');

        $total = $countryNewsQuery->count();

        if ($total === 0) {
            return $this->scoreNewsGlobalFallback();
        }

        $positive = (clone $countryNewsQuery)
            ->where('sentiment', 'positive')
            ->count();

        $neutral = (clone $countryNewsQuery)
            ->where('sentiment', 'neutral')
            ->count();

        $negative = (clone $countryNewsQuery)
            ->where('sentiment', 'negative')
            ->count();

        return (($positive * 20) + ($neutral * 50) + ($negative * 80)) / $total;
    }

    /**
     * Fallback jika negara belum memiliki berita sendiri.
     */
    protected function scoreNewsGlobalFallback(): float
    {
        $total = NewsArticle::whereNull('country_id')
            ->whereNotNull('sentiment')
            ->count();

        if ($total === 0) {
            return 50;
        }

        $positive = NewsArticle::whereNull('country_id')
            ->where('sentiment', 'positive')
            ->count();

        $neutral = NewsArticle::whereNull('country_id')
            ->where('sentiment', 'neutral')
            ->count();

        $negative = NewsArticle::whereNull('country_id')
            ->where('sentiment', 'negative')
            ->count();

        return (($positive * 20) + ($neutral * 50) + ($negative * 80)) / $total;
    }

    protected function scoreCurrency(): float
    {
        // Placeholder netral.
        // Nanti dapat dikembangkan menggunakan histori currency_rates
        // untuk menghitung volatilitas kurs.
        return 50;
    }
}