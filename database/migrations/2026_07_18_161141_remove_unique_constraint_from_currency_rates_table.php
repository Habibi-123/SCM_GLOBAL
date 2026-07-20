<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('currency_rates', function (Blueprint $table) {
            // Hapus constraint unique supaya bisa menyimpan banyak snapshot
            // dari waktu ke waktu (histori), bukan cuma 1 baris per pasangan mata uang
            $table->dropUnique(['base_currency', 'target_currency']);
        });
    }

    public function down(): void
    {
        Schema::table('currency_rates', function (Blueprint $table) {
            $table->unique(['base_currency', 'target_currency']);
        });
    }
};