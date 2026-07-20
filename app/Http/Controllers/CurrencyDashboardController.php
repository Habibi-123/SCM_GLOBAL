<?php

namespace App\Http\Controllers;

use App\Models\CurrencyRate;
use Illuminate\Http\Request;

class CurrencyDashboardController extends Controller
{
    public function index(Request $request)
    {
        $base = strtoupper($request->input('base', 'USD'));
        $target = strtoupper($request->input('target', 'IDR'));

        // Snapshot terkini untuk semua mata uang (base yang sama)
        $latestRates = CurrencyRate::where('base_currency', $base)
            ->whereIn('id', function ($query) use ($base) {
                $query->selectRaw('MAX(id)')
                    ->from('currency_rates')
                    ->where('base_currency', $base)
                    ->groupBy('target_currency');
            })
            ->orderBy('target_currency')
            ->paginate(20);

        // Histori untuk grafik tren 1 pasangan mata uang spesifik
        $history = CurrencyRate::where('base_currency', $base)
            ->where('target_currency', $target)
            ->orderBy('fetched_at')
            ->get(['rate', 'fetched_at']);

        return view('currency.index', compact('base', 'target', 'latestRates', 'history'));
    }
}