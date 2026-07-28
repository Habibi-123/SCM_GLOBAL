<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Services\CountryNewsService;
use App\Services\RiskScoringService;
use Illuminate\Http\Request;

class CountryDashboardController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $countries = Country::query()
            ->when($search, fn ($query) => $query->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate(24);

        return view('countries.index', compact('countries', 'search'));
    }

    public function show(Country $country, CountryNewsService $countryNewsService, RiskScoringService $riskScoringService)
    {
        // Eager load relasi supaya tidak N+1 query saat diakses di Blade
        $country->load(['latestEconomicIndicator']);

        // Hitung ulang Risk Score secara real-time saat halaman detail negara dibuka
        try {
            $calculatedScore = $riskScoringService->calculateForCountry($country);
            $country->riskScores()->create([
                'weather_score'   => $calculatedScore['weather_score'],
                'inflation_score' => $calculatedScore['inflation_score'],
                'exchange_score'  => $calculatedScore['exchange_score'],
                'news_score'      => $calculatedScore['news_score'],
                'total_score'     => $calculatedScore['total_score'],
                'risk_level'      => $calculatedScore['risk_level'],
                'calculated_at'   => now(),
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("Risk calculation failed for {$country->name}: " . $e->getMessage());
        }

        $country->load('latestRiskScore');

        $latestWeather = $country->weatherData()
            ->latest('fetched_at')
            ->first();

        // Ambil kurs mata uang terbaru negara ini terhadap USD
        $currencyRate = \App\Models\CurrencyRate::where('base_currency', 'USD')
            ->where('target_currency', $country->currency_code)
            ->latest('fetched_at')
            ->first();

        // Data untuk grafik tren risk score
        $riskHistory = $country->riskScores()
            ->orderBy('calculated_at')
            ->get(['total_score', 'calculated_at']);

        // Ambil berita spesifik negara (menggunakan cache agar hemat kuota GNews)
        $countryNews = $countryNewsService->getNewsForCountry($country);



        return view('countries.show', compact(
            'country',
            'latestWeather',
            'currencyRate',
            'riskHistory',
            'countryNews'
        ));
    }

    public function refreshNews(Country $country, CountryNewsService $countryNewsService)
    {
        // Rate limit sederhana: cegah refresh manual lebih dari 1x per 10 menit per negara,
        // supaya tidak sengaja/iseng spam klik dan menghabiskan kuota API
        $rateLimitKey = "news_refresh_limit:{$country->code}";

        if (\Illuminate\Support\Facades\Cache::has($rateLimitKey)) {
            return back()->with('error', 'Tunggu beberapa menit sebelum refresh berita lagi.');
        }

        $countryNewsService->refreshNewsForCountry($country);

        \Illuminate\Support\Facades\Cache::put($rateLimitKey, true, now()->addMinutes(10));

        return back()->with('success', 'Berita berhasil diperbarui.');
    }
}