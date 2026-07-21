<?php

namespace Database\Seeders;

use App\Models\CurrencyRate;
use Illuminate\Database\Seeder;

class CurrencyHistorySeeder extends Seeder
{
    public function run(): void
    {
        // Ambil snapshot TERBARU untuk setiap mata uang (hasil currency:sync),
        // lalu jadikan basis untuk membuat variasi histori 14 hari.
        // Catatan: ini DATA SIMULASI untuk keperluan demo (menghitung volatilitas
        // di Risk Scoring Engine), BUKAN data kurs historis asli — karena API
        // gratis yang kita pakai tidak menyediakan data historis sungguhan.
        $latestRates = CurrencyRate::where('base_currency', 'USD')
            ->whereIn('id', function ($query) {
                $query->selectRaw('MAX(id)')
                    ->from('currency_rates')
                    ->where('base_currency', 'USD')
                    ->groupBy('target_currency');
            })
            ->get();

        if ($latestRates->isEmpty()) {
            $this->command->warn('Belum ada data kurs sama sekali. Jalankan currency:sync dulu.');
            return;
        }

        $bar = $this->command->getOutput()->createProgressBar($latestRates->count());
        $bar->start();

        foreach ($latestRates as $latest) {
            $baseRate = (float) $latest->rate;

            // Variasi acak berbeda-beda per mata uang, supaya tidak semua negara
            // punya volatilitas yang persis sama (lebih realistis untuk demo)
            $volatilityFactor = rand(50, 300) / 10000; // antara 0.5% - 3%

            for ($daysAgo = 14; $daysAgo >= 1; $daysAgo--) {
                $variation = $baseRate * (rand(-100, 100) / 100 * $volatilityFactor);

                CurrencyRate::create([
                    'base_currency' => 'USD',
                    'target_currency' => $latest->target_currency,
                    'rate' => round($baseRate + $variation, 6),
                    'fetched_at' => now()->subDays($daysAgo),
                    'created_at' => now()->subDays($daysAgo),
                ]);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->command->newLine();
        $this->command->info($latestRates->count() . ' mata uang berhasil dibuat data histori 14 hari (data simulasi untuk demo).');
    }
}