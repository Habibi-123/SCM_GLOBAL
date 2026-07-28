<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\CurrencyRate;
use App\Services\CountryNewsService;
use Illuminate\Http\Request;

use App\Services\RiskScoringService;

class DashboardController extends Controller
{
    public function index(Request $request, CountryNewsService $countryNewsService, RiskScoringService $riskScoringService)
    {
        $countries = Country::orderBy('name')->get(['code', 'name', 'flag_url']);

        $selectedCode = $request->input('country');

        if (!$selectedCode) {
            return view('dashboard', [
                'countries' => $countries,
                'country' => null,
            ]);
        }

        $country = Country::where('code', $selectedCode)
            ->with(['latestEconomicIndicator'])
            ->firstOrFail();

        // Hitung skor risiko terkini secara real-time
        $calculatedScore = $riskScoringService->calculateForCountry($country);
        $country->riskScores()->create([
            'weather_score' => $calculatedScore['weather_score'],
            'inflation_score' => $calculatedScore['inflation_score'],
            'exchange_score' => $calculatedScore['exchange_score'],
            'news_score' => $calculatedScore['news_score'],
            'total_score' => $calculatedScore['total_score'],
            'risk_level' => $calculatedScore['risk_level'],
            'calculated_at' => now(),
        ]);
        $country->load('latestRiskScore');

        $latestWeather = $country->weatherData()->latest('fetched_at')->first();

        $currencyRate = CurrencyRate::where('base_currency', 'USD')
            ->where('target_currency', $country->currency_code)
            ->latest('fetched_at')
            ->first();

        $riskHistory = $country->riskScores()
            ->orderBy('calculated_at')
            ->get(['total_score', 'calculated_at']);

        $economicHistory = $country->economicIndicators()
        ->orderBy('year')
        ->get(['year', 'gdp', 'inflation', 'exports', 'imports']);

    $countryNews = $countryNewsService->getNewsForCountry($country);

    $ports = $country->ports()->get(['id', 'name', 'latitude', 'longitude']);

    return view('dashboard', compact(
        'countries', 'country', 'latestWeather', 'currencyRate',
        'riskHistory', 'economicHistory', 'countryNews', 'ports', 'selectedCode'
        ));
    }
}