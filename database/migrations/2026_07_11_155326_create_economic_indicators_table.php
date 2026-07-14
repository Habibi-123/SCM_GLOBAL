<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('economic_indicators', function (Blueprint $table) {
            $table->id();

            // Foreign key ke tabel countries, cascade delete artinya
            // jika negara dihapus, data ekonominya ikut terhapus otomatis
            $table->foreignId('country_id')->constrained()->cascadeOnDelete();

            $table->decimal('gdp', 20, 2)->nullable();        // GDP dalam USD
            $table->decimal('inflation', 6, 2)->nullable();    // Persentase inflasi, contoh: 3.25
            $table->decimal('exports', 20, 2)->nullable();     // Nilai ekspor
            $table->decimal('imports', 20, 2)->nullable();     // Nilai impor
            $table->unsignedSmallInteger('year');               // Tahun data, contoh: 2024

            $table->timestamps();

            // Mencegah data duplikat: satu negara hanya boleh punya
            // 1 baris data ekonomi per tahun
            $table->unique(['country_id', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('economic_indicators');
    }
};