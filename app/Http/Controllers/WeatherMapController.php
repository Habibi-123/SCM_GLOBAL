<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\WeatherData;
use App\Services\OpenMeteoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class WeatherMapController extends Controller
{
    protected int $staleHours = 6; // anggap data "basi" kalau sudah lebih dari 6 jam

    public function index()
    {
        return view('weather.index');
    }

    public function data(Request $request, OpenMeteoService $service)
    {
        $this->refreshIfStale($service);

        $countries = Country::query()
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->with(['weatherData' => fn ($q) => $q->latest('fetched_at')->limit(1)])
            ->get(['id', 'name', 'code', 'latitude', 'longitude']);

        $result = $countries->map(function ($country) {
            $weather = $country->weatherData->first();

            return [
                'name' => $country->name,
                'code' => $country->code,
                'latitude' => $country->latitude,
                'longitude' => $country->longitude,
                'temperature' => $weather?->temperature,
                'rainfall' => $weather?->rainfall,
                'wind_speed' => $weather?->wind_speed,
                'storm_risk' => $weather?->storm_risk ?? 'low',
            ];
        });

        return response()->json($result);
    }

    protected function refreshIfStale(OpenMeteoService $service): void
    {
        $cacheKey = 'weather_last_refresh';

        // Kalau sudah pernah dicek dalam 6 jam terakhir, jangan fetch ulang
        if (Cache::has($cacheKey)) {
            return;
        }

        $countries = Country::whereNotNull('latitude')->whereNotNull('longitude')->get();

        $locations = $countries->mapWithKeys(fn ($country) => [
            $country->id => ['lat' => $country->latitude, 'lng' => $country->longitude],
        ])->toArray();

        $weatherResults = $service->getBulkWeather($locations);

        foreach ($weatherResults as $countryId => $weather) {
            WeatherData::create([
                'country_id'  => $countryId,
                'temperature' => $weather['temperature'],
                'rainfall'    => $weather['rainfall'],
                'wind_speed'  => $weather['wind_speed'],
                'storm_risk'  => $weather['storm_risk'],
                'fetched_at'  => now(),
            ]);
        }

        // Tandai sudah di-refresh, supaya tidak fetch ulang lagi
        // dalam 6 jam ke depan setiap kali menu Weather dibuka
        Cache::put($cacheKey, true, now()->addHours($this->staleHours));

        // Sekalian hitung ulang risk score, karena data cuaca baru saja berubah
        \Illuminate\Support\Facades\Artisan::call('risk:calculate');
    }
}