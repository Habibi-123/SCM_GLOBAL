<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;

class WatchlistController extends Controller
{
    public function index(Request $request)
    {
        $watchedCountries = $request->user()
            ->watchedCountries()
            ->with(['latestEconomicIndicator', 'latestRiskScore'])
            ->get();

        return view('watchlist.index', compact('watchedCountries'));
    }

    public function store(Request $request, Country $country)
    {
        $request->user()->watchedCountries()->syncWithoutDetaching([$country->id]);

        return back()->with('success', "{$country->name} ditambahkan ke watchlist.");
    }

    public function destroy(Request $request, Country $country)
    {
        $request->user()->watchedCountries()->detach($country->id);

        return back()->with('success', "{$country->name} dihapus dari watchlist.");
    }
}