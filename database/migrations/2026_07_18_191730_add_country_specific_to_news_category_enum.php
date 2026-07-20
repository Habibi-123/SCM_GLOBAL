<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE news_articles MODIFY COLUMN category ENUM('logistics', 'trade', 'shipping', 'economy', 'geopolitics', 'country-specific') NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE news_articles MODIFY COLUMN category ENUM('logistics', 'trade', 'shipping', 'economy', 'geopolitics') NULL");
    }
};