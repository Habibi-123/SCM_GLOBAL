<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CountryResource;
use App\Models\Country;
use Illuminate\Http\Request;

class CountryApiController extends Controller
{
    // GET /api/countries
    public function index(Request $request)
    {
        $countries = Country::query()
            ->when($request->filled('search'), fn ($q) =>
                $q->where('name', 'like', '%' . $request->input('search') . '%'))
            ->when($request->filled('region'), fn ($q) =>
                $q->where('region', $request->input('region')))
            ->orderBy('name')
            ->paginate($request->input('per_page', 25));

        return CountryResource::collection($countries);
    }

    // GET /api/countries/{code}
    public function show(Country $country)
    {
        $country->load(['latestEconomicIndicator', 'latestRiskScore']);

        return new CountryResource($country);
    }

    // GET /api/risk (semua negara dengan risk score terkini)
    public function risk(Request $request)
    {
        $countries = Country::query()
            ->with('latestRiskScore')
            ->when($request->filled('level'), fn ($q) =>
                $q->whereHas('latestRiskScore', fn ($rq) => $rq->where('risk_level', $request->input('level'))))
            ->whereHas('latestRiskScore')
            ->paginate($request->input('per_page', 25));

        return CountryResource::collection($countries);
    }
}