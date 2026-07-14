<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('currency_rates', function (Blueprint $table) {
            $table->id();

            $table->string('base_currency', 3);   // Mata uang dasar, contoh: USD
            $table->string('target_currency', 3); // Mata uang tujuan, contoh: IDR
            $table->decimal('rate', 15, 6);        // Nilai kurs, contoh: 1 USD = 15750.500000 IDR

            $table->timestamp('fetched_at')->nullable(); // Kapan data diambil dari ExchangeRate API
            $table->timestamps();

            // Kombinasi base+target tidak boleh duplikat, tapi bisa di-update
            $table->unique(['base_currency', 'target_currency']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('currency_rates');
    }
};