<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('weather_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained()->cascadeOnDelete();

            $table->decimal('temperature', 5, 2)->nullable();  // Suhu dalam Celsius
            $table->decimal('rainfall', 6, 2)->nullable();     // Curah hujan (mm)
            $table->decimal('wind_speed', 6, 2)->nullable();   // Kecepatan angin (km/jam)
            $table->enum('storm_risk', ['low', 'medium', 'high'])->default('low'); // Level risiko badai

            $table->timestamp('fetched_at')->nullable(); // Kapan data ini terakhir diambil dari Open-Meteo API
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weather_data');
    }
};