<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\CurrencyRate;
use Illuminate\Http\Request;

class CurrencyDashboardController extends Controller
{
    public function index(Request $request)
    {
        $base = 'USD'; // basis tetap USD, konsisten dengan sync yang sudah dilakukan
        $countries = Country::whereNotNull('currency_code')->orderBy('name')->get(['code', 'name', 'currency_code']);

        $selectedCode = $request->input('country', 'IDN'); // default Indonesia
        $selectedCountry = Country::where('code', $selectedCode)->first();
        $target = $selectedCountry->currency_code ?? 'IDR';

        // Snapshot terkini untuk semua mata uang (tabel referensi di bawah grafik)
        $latestRates = CurrencyRate::where('base_currency', $base)
            ->whereIn('id', function ($query) use ($base) {
                $query->selectRaw('MAX(id)')
                    ->from('currency_rates')
                    ->where('base_currency', $base)
                    ->groupBy('target_currency');
            })
            ->orderBy('target_currency')
            ->paginate(20);

        // Histori untuk grafik tren, khusus mata uang negara yang dipilih
        $history = CurrencyRate::where('base_currency', $base)
            ->where('target_currency', $target)
            ->orderBy('fetched_at')
            ->get(['rate', 'fetched_at']);

        return view('currency.index', compact(
            'base', 'target', 'countries', 'selectedCountry', 'selectedCode', 'latestRates', 'history'
        ));
    }
}