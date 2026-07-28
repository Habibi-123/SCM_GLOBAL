<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\CurrencyRate;
use App\Services\ExchangeRateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CurrencyDashboardController extends Controller
{
    public function index(Request $request, ExchangeRateService $exchangeRateService)
    {
        $base = 'USD';
        $countries = Country::whereNotNull('currency_code')->orderBy('name')->get(['code', 'name', 'currency_code']);

        $selectedCode = $request->input('country', 'IDN');
        $selectedCountry = Country::where('code', $selectedCode)->first();
        $target = $selectedCountry->currency_code ?? 'IDR';

        // Fetch kurs terbaru dari API secara real-time, di-cache 1 jam
        // agar tidak spam ke API setiap kali halaman di-refresh
        $cacheKey = "currency_live_{$base}";
        if (!Cache::has($cacheKey)) {
            $rates = $exchangeRateService->getRates($base);

            if (!empty($rates)) {
                foreach ($rates as $targetCurrency => $rate) {
                    CurrencyRate::create([
                        'base_currency'   => $base,
                        'target_currency' => $targetCurrency,
                        'rate'            => $rate,
                        'fetched_at'      => now(),
                    ]);
                }
                // Tandai sudah di-fetch, jangan fetch ulang dalam 1 jam
                Cache::put($cacheKey, true, now()->addHour());
            }
        }

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

        // Histori 7 hari terakhir (1 data per hari, diambil dari update jam terbaru)
        $history = CurrencyRate::where('base_currency', $base)
            ->where('target_currency', $target)
            ->where('fetched_at', '>=', now()->subDays(6)->startOfDay())
            ->get()
            ->groupBy(function ($item) {
                return \Carbon\Carbon::parse($item->fetched_at)->format('Y-m-d');
            })
            ->map(function ($dayRates) {
                return $dayRates->sortByDesc('fetched_at')->first();
            })
            ->values();

        return view('currency.index', compact(
            'base', 'target', 'countries', 'selectedCountry', 'selectedCode', 'latestRates', 'history'
        ));
    }
}