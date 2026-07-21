<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\CurrencyRate;
use App\Services\CountryNewsService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request, CountryNewsService $countryNewsService)
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
            ->with(['latestEconomicIndicator', 'latestRiskScore'])
            ->firstOrFail();

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