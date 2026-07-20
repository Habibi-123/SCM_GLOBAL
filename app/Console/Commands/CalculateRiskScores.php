<?php

namespace App\Console\Commands;

use App\Models\Country;
use App\Models\RiskScore;
use App\Services\RiskScoringService;
use Illuminate\Console\Command;

class CalculateRiskScores extends Command
{
    protected $signature = 'risk:calculate';

    protected $description = 'Hitung skor risiko semua negara berdasarkan cuaca, inflasi, berita, dan kurs';

    public function handle(RiskScoringService $service): int
    {
        $countries = Country::all();
        $this->info("Menghitung risk score untuk {$countries->count()} negara...");

        $bar = $this->output->createProgressBar($countries->count());
        $bar->start();

        foreach ($countries as $country) {
            $result = $service->calculateForCountry($country);

            RiskScore::create([
                'country_id' => $country->id,
                'weather_score' => $result['weather_score'],
                'inflation_score' => $result['inflation_score'],
                'exchange_score' => $result['exchange_score'],
                'news_score' => $result['news_score'],
                'total_score' => $result['total_score'],
                'risk_level' => $result['risk_level'],
                'calculated_at' => now(),
            ]);

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info('Perhitungan risk score selesai untuk semua negara!');

        return self::SUCCESS;
    }
}