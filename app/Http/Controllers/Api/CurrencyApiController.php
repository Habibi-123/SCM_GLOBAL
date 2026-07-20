<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CurrencyRateResource;
use App\Models\CurrencyRate;
use Illuminate\Http\Request;

class CurrencyApiController extends Controller
{
    // GET /api/currency
    public function index(Request $request)
    {
        $rates = CurrencyRate::query()
            ->when($request->filled('base'), fn ($q) =>
                $q->where('base_currency', strtoupper($request->input('base'))))
            ->when($request->filled('target'), fn ($q) =>
                $q->where('target_currency', strtoupper($request->input('target'))))
            ->paginate($request->input('per_page', 50));

        return CurrencyRateResource::collection($rates);
    }
}