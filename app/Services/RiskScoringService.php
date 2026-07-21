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
        $currencyScore = $this->scoreCurrency($country);

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
        if (!$country->currency_code) {
        return 50; // tidak ada mata uang (misal Antarctica), anggap netral
    }

    $rates = \App\Models\CurrencyRate::where('base_currency', 'USD')
        ->where('target_currency', $country->currency_code)
        ->orderBy('fetched_at')
        ->pluck('rate')
        ->map(fn ($r) => (float) $r);

    // Butuh minimal 2 titik data untuk bisa menghitung volatilitas.
    // Kalau cuma ada 1 snapshot (belum ada histori), tidak bisa dihitung → netral.
    if ($rates->count() < 2) {
        return 50;
    }

    $mean = $rates->avg();

    if ($mean == 0) {
        return 50;
    }

    // Standar deviasi manual (tidak ada helper bawaan Laravel Collection untuk ini)
    $variance = $rates->reduce(fn ($carry, $rate) => $carry + pow($rate - $mean, 2), 0) / $rates->count();
    $stdDev = sqrt($variance);

    // Coefficient of variation dalam persen: seberapa besar fluktuasi
    // relatif terhadap rata-rata kursnya
    $coefficientOfVariation = ($stdDev / $mean) * 100;

    // Skala ke 0-100: kurs mata uang mayor biasanya berfluktuasi <1% dalam
    // rentang pendek, jadi kita kalikan faktor skala supaya sensitif
    $score = min(100, $coefficientOfVariation * 20);

    return $score;
    }
}