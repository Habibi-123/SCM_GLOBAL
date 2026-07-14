<?php

namespace App\Console\Commands;

use App\Models\Country;
use App\Models\EconomicIndicator;
use App\Services\WorldBankService;
use Illuminate\Console\Command;

class SyncEconomicIndicators extends Command
{
    protected $signature = 'economic:sync {--year=2023}';

    protected $description = 'Sinkronisasi data GDP, inflasi, ekspor, impor dari World Bank API';

    public function handle(WorldBankService $service): int
    {
        $year = (int) $this->option('year');
        $this->info("Mengambil data ekonomi tahun {$year} dari World Bank API...");

        $gdp = $service->getIndicatorForAllCountries('NY.GDP.MKTP.CD', $year);
        $inflation = $service->getIndicatorForAllCountries('FP.CPI.TOTL.ZG', $year);
        $exports = $service->getIndicatorForAllCountries('NE.EXP.GNFS.CD', $year);
        $imports = $service->getIndicatorForAllCountries('NE.IMP.GNFS.CD', $year);

        $countries = Country::all();
        $bar = $this->output->createProgressBar($countries->count());
        $bar->start();

        $synced = 0;

        foreach ($countries as $country) {
            $code = $country->code;

            if (!isset($gdp[$code], $inflation[$code], $exports[$code], $imports[$code])
                && !isset($gdp[$code]) && !isset($inflation[$code])
                && !isset($exports[$code]) && !isset($imports[$code])) {
                $bar->advance();
                continue;
            }

            EconomicIndicator::updateOrCreate(
                ['country_id' => $country->id, 'year' => $year],
                [
                    'gdp' => $gdp[$code] ?? null,
                    'inflation' => $inflation[$code] ?? null,
                    'exports' => $exports[$code] ?? null,
                    'imports' => $imports[$code] ?? null,
                ]
            );

            $synced++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Sinkronisasi selesai! {$synced} negara berhasil disimpan untuk tahun {$year}.");

        return self::SUCCESS;
    }
}