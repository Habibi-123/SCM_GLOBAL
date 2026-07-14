<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained()->cascadeOnDelete();

            $table->string('name');           // Nama pelabuhan, contoh: Tanjung Priok
            $table->string('unlocode', 10)->nullable(); // Kode standar internasional pelabuhan (UN/LOCODE)
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);

            $table->timestamps();

            $table->index('name'); // Index untuk mempercepat fitur "cari pelabuhan"
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ports');
    }
};