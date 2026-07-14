<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('risk_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained()->cascadeOnDelete();

            // Skor per-komponen (0-100), sesuai bobot di spesifikasi:
            // Weather 30%, Inflation 20%, Political News 40%, Currency 10%
            $table->decimal('weather_score', 5, 2)->default(0);
            $table->decimal('inflation_score', 5, 2)->default(0);
            $table->decimal('exchange_score', 5, 2)->default(0);
            $table->decimal('news_score', 5, 2)->default(0);

            $table->decimal('total_score', 5, 2)->default(0); // Hasil akhir perhitungan weighted
            $table->enum('risk_level', ['low', 'medium', 'high'])->default('low');

            $table->timestamp('calculated_at')->nullable(); // Kapan skor ini terakhir dihitung ulang
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('risk_scores');
    }
};