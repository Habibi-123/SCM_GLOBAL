<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('news_articles', function (Blueprint $table) {
            $table->id();

            // nullable karena tidak semua berita otomatis ke-tag ke satu negara spesifik
            $table->foreignId('country_id')->nullable()->constrained()->nullOnDelete();

            $table->string('title');
            $table->string('url')->unique(); // Mencegah berita yang sama tersimpan dua kali
            $table->string('source')->nullable(); // Nama media, contoh: Reuters, BBC
            $table->enum('category', ['logistics', 'trade', 'shipping', 'economy', 'geopolitics'])->nullable();

            $table->unsignedTinyInteger('positive_count')->default(0); // Jumlah kata positif ditemukan
            $table->unsignedTinyInteger('negative_count')->default(0); // Jumlah kata negatif ditemukan
            $table->enum('sentiment', ['positive', 'neutral', 'negative'])->nullable(); // Hasil akhir analisis

            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('news_articles');
    }
};