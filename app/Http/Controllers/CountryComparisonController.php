<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;

class CountryComparisonController extends Controller
{
    public function index(Request $request)
    {
        $countries = Country::orderBy('name')->get(['id', 'code', 'name', 'flag_url']);

        $countryA = null;
        $countryB = null;

        if ($request->filled('country_a')) {
            $countryA = Country::where('code', $request->input('country_a'))
                ->with(['latestEconomicIndicator', 'latestRiskScore'])
                ->first();
            $countryA?->setRelation('latestWeather', $countryA->weatherData()->latest('fetched_at')->first());
        }

        if ($request->filled('country_b')) {
            $countryB = Country::where('code', $request->input('country_b'))
                ->with(['latestEconomicIndicator', 'latestRiskScore'])
                ->first();
            $countryB?->setRelation('latestWeather', $countryB->weatherData()->latest('fetched_at')->first());
        }

        return view('compare.index', compact('countries', 'countryA', 'countryB'));
    }
}