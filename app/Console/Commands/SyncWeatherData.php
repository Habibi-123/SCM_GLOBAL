<?php

namespace App\Console\Commands;

use App\Models\Country;
use App\Models\WeatherData;
use App\Services\OpenMeteoService;
use Illuminate\Console\Command;

class SyncWeatherData extends Command
{
    protected $signature = 'weather:sync';

    protected $description = 'Sinkronisasi data cuaca terkini dari Open-Meteo API untuk semua negara';

    public function handle(OpenMeteoService $service): int
    {
        $this->info('Mengambil data cuaca dari Open-Meteo API...');

        $countries = Country::whereNotNull('latitude')->whereNotNull('longitude')->get();

        $locations = $countries->mapWithKeys(fn ($country) => [
            $country->id => ['lat' => $country->latitude, 'lng' => $country->longitude],
        ])->toArray();

        $weatherResults = $service->getBulkWeather($locations);

        $bar = $this->output->createProgressBar(count($weatherResults));
        $bar->start();

        foreach ($weatherResults as $countryId => $weather) {
            WeatherData::create([
                'country_id'  => $countryId,
                'temperature' => $weather['temperature'],
                'rainfall'    => $weather['rainfall'],
                'wind_speed'  => $weather['wind_speed'],
                'storm_risk'  => $weather['storm_risk'],
                'fetched_at'  => now(),
            ]);

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info('Sinkronisasi cuaca selesai! Total data tersimpan: ' . count($weatherResults));

        return self::SUCCESS;
    }
}