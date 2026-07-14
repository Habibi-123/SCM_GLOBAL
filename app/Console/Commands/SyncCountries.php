<?php

namespace App\Console\Commands;

use App\Models\Country;
use App\Services\RestCountriesService;
use Illuminate\Console\Command;

class SyncCountries extends Command
{
    protected $signature = 'countries:sync';

    protected $description = 'Sinkronisasi data dasar negara dari REST Countries API ke tabel countries';

    public function handle(RestCountriesService $service): int
    {
        $this->info('Mengambil data negara dari REST Countries API...');

        $countries = $service->getAllCountries();

        if (empty($countries)) {
            $this->error('Gagal mengambil data. Cek koneksi atau log error.');
            return self::FAILURE;
        }

        $bar = $this->output->createProgressBar(count($countries));
        $bar->start();

        foreach ($countries as $item) {
            $code = $item['codes']['alpha_3'] ?? null;

            if (empty($code)) {
                continue;
            }

            $currencyCode = $item['currencies'][0]['code'] ?? null;

            $capitals = $item['capitals'] ?? [];
            $primaryCapital = collect($capitals)->firstWhere('attributes.primary', true) ?? ($capitals[0] ?? null);

            Country::updateOrCreate(
                ['code' => $code],
                [
                    'name' => $item['names']['common'] ?? '-',
                    'code_alpha2' => $item['codes']['alpha_2'] ?? null,
                    'currency_code' => $currencyCode,
                    'region' => $item['region'] ?? null,
                    'capital' => $primaryCapital['name'] ?? null,
                    'population' => $item['population'] ?? null,
                    'flag_url' => $item['flag']['url_png'] ?? null,
                    'latitude' => $item['coordinates']['lat'] ?? null,
                    'longitude' => $item['coordinates']['lng'] ?? null,
                ]
            );

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info('Sinkronisasi selesai! Total negara: ' . Country::count());

        return self::SUCCESS;
    }
}