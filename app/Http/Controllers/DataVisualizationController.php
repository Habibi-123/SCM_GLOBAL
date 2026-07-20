<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;

class DataVisualizationController extends Controller
{
    public function index(Request $request)
    {
        $countries = Country::orderBy('name')->get(['code', 'name']);
        $selectedCode = $request->input('country', 'IDN'); // default Indonesia

        $country = Country::where('code', $selectedCode)->first();

        $economicHistory = $country
            ? $country->economicIndicators()->orderBy('year')->get(['year', 'gdp', 'inflation', 'exports', 'imports'])
            : collect();

        $riskHistory = $country
            ? $country->riskScores()->orderBy('calculated_at')->get(['total_score', 'calculated_at'])
            : collect();

        return view('visualization.index', compact('countries', 'country', 'economicHistory', 'riskHistory', 'selectedCode'));
    }
}