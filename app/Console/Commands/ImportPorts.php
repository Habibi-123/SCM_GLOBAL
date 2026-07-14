<?php

namespace App\Console\Commands;

use App\Models\Country;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportPorts extends Command
{
    protected $signature = 'ports:import {file=world_port_index.csv}';

    protected $description = 'Import data pelabuhan dari CSV World Port Index ke tabel ports';

    public function handle(): int
    {
        $filename = $this->argument('file');
        $path = storage_path("app/datasets/{$filename}");

        if (!file_exists($path)) {
            $this->error("File tidak ditemukan di: {$path}");
            return self::FAILURE;
        }

        $countryLookup = Country::whereNotNull('code_alpha2')->pluck('id', 'code_alpha2')->toArray();

        $handle = fopen($path, 'r');
        $headers = fgetcsv($handle);

        $imported = 0;
        $skipped = 0;
        $buffer = [];
        $batchSize = 500;

        $this->info('Mengimpor data pelabuhan...');

        while (($row = fgetcsv($handle)) !== false) {
            $data = array_combine($headers, $row);

            $countryAlpha2 = trim($data['COUNTRY'] ?? '');
            $countryId = $countryLookup[$countryAlpha2] ?? null;

            $latitude = is_numeric($data['LATITUDE'] ?? null) ? (float) $data['LATITUDE'] : null;
            $longitude = is_numeric($data['LONGITUDE'] ?? null) ? (float) $data['LONGITUDE'] : null;

            if (!$countryId || !$latitude || !$longitude) {
                $skipped++;
                continue;
            }

            $buffer[] = [
                'country_id' => $countryId,
                'name' => trim($data['PORT_NAME'] ?? '-'),
                'unlocode' => $data['INDEX_NO'] ?? null, 
                'latitude' => $latitude,
                'longitude' => $longitude,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $imported++;

            if (count($buffer) >= $batchSize) {
                DB::table('ports')->insert($buffer);
                $buffer = [];
                $this->output->write('.'); 
            }
        }

        if (!empty($buffer)) {
            DB::table('ports')->insert($buffer);
        }

        fclose($handle);

        $this->newLine(2);
        $this->info("Import selesai! Berhasil: {$imported}, dilewati: {$skipped}.");

        return self::SUCCESS;
    }
}