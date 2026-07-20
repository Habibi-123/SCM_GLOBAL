<?php

namespace Database\Seeders;

use App\Models\CurrencyRate;
use Illuminate\Database\Seeder;

class CurrencyHistorySeeder extends Seeder
{
    public function run(): void
    {
        // Ambil rate IDR terkini sebagai basis, lalu buat variasi histori
        // untuk 14 hari terakhir supaya grafik tren ada bentuknya.
        // Catatan: ini DATA SIMULASI untuk demo, bukan data historis asli
        // (API gratis kita cuma kasih snapshot terkini, bukan histori).
        $latestIdr = CurrencyRate::where('base_currency', 'USD')
            ->where('target_currency', 'IDR')
            ->latest('fetched_at')
            ->first();

        if (!$latestIdr) {
            $this->command->warn('Belum ada data USD-IDR. Jalankan currency:sync dulu.');
            return;
        }

        $baseRate = (float) $latestIdr->rate;

        for ($daysAgo = 14; $daysAgo >= 1; $daysAgo--) {
            // Variasi acak kecil (±1%) supaya grafik terlihat wajar, bukan garis lurus
            $variation = $baseRate * (rand(-100, 100) / 10000);

            CurrencyRate::create([
                'base_currency' => 'USD',
                'target_currency' => 'IDR',
                'rate' => round($baseRate + $variation, 2),
                'fetched_at' => now()->subDays($daysAgo),
                'created_at' => now()->subDays($daysAgo),
            ]);
        }

        $this->command->info('14 hari data histori kurs USD-IDR berhasil dibuat (data simulasi untuk demo).');
    }
}