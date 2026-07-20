<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncEconomicHistoryRange extends Command
{
    protected $signature = 'economic:sync-range {--start=2019} {--end=2023}';

    protected $description = 'Sinkronisasi data ekonomi untuk rentang tahun sekaligus (untuk grafik tren historis)';

    public function handle(): int
    {
        $start = (int) $this->option('start');
        $end = (int) $this->option('end');

        for ($year = $start; $year <= $end; $year++) {
            $this->info("=== Sinkronisasi tahun {$year} ===");
            $this->call('economic:sync', ['--year' => $year]); // reuse command yang sudah ada
        }

        $this->info('Sinkronisasi rentang tahun selesai.');
        return self::SUCCESS;
    }
}