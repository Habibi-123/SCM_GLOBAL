<?php

namespace App\Console\Commands;

use App\Models\CurrencyRate;
use App\Services\ExchangeRateService;
use Illuminate\Console\Command;

class SyncCurrencyRates extends Command
{
    protected $signature = 'currency:sync {--base=USD}';

    protected $description = 'Sinkronisasi kurs mata uang dari ExchangeRate API';

    public function handle(ExchangeRateService $service): int
    {
        $base = strtoupper($this->option('base'));
        $this->info("Mengambil kurs mata uang berbasis {$base}...");

        $rates = $service->getRates($base);

        if (empty($rates)) {
            $this->error('Gagal mengambil data kurs. Cek koneksi atau log error.');
            return self::FAILURE;
        }

        $bar = $this->output->createProgressBar(count($rates));
        $bar->start();

        foreach ($rates as $targetCurrency => $rate) {
            CurrencyRate::updateOrCreate(
                ['base_currency' => $base, 'target_currency' => $targetCurrency],
                ['rate' => $rate, 'fetched_at' => now()]
            );

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info('Sinkronisasi kurs selesai! Total: ' . count($rates) . ' mata uang.');

        return self::SUCCESS;
    }
}