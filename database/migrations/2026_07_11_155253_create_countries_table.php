<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id(); // Primary key auto-increment (unsigned bigint)

            $table->string('name');           // Nama negara, contoh: Indonesia
            $table->string('code', 3)->unique(); // Kode negara ISO, contoh: IDN, DEU (dari REST Countries API)
            $table->string('currency_code', 3); // Kode mata uang, contoh: IDR, USD (bukan FK, karena 1 currency bisa dipakai banyak negara)
            $table->string('region')->nullable(); // Benua/wilayah, contoh: Asia, Europe
            $table->string('capital')->nullable(); // Ibu kota negara
            $table->bigInteger('population')->nullable(); // Jumlah penduduk (data terbaru dari REST Countries)
            $table->string('flag_url')->nullable(); // URL gambar bendera negara
            $table->decimal('latitude', 10, 7)->nullable();  // Koordinat untuk marker di peta (Leaflet.js)
            $table->decimal('longitude', 10, 7)->nullable();

            $table->timestamps(); // created_at & updated_at otomatis
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};