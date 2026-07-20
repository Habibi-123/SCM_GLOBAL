<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('risk_scores', function (Blueprint $table) {
            $table->foreignId('country_id')->after('id')->constrained()->cascadeOnDelete();
            $table->decimal('weather_score', 5, 2)->default(0)->after('country_id');
            $table->decimal('inflation_score', 5, 2)->default(0)->after('weather_score');
            $table->decimal('exchange_score', 5, 2)->default(0)->after('inflation_score');
            $table->decimal('news_score', 5, 2)->default(0)->after('exchange_score');
            $table->decimal('total_score', 5, 2)->default(0)->after('news_score');
            $table->enum('risk_level', ['low', 'medium', 'high'])->default('low')->after('total_score');
            $table->timestamp('calculated_at')->nullable()->after('risk_level');
        });
    }

    public function down(): void
    {
        Schema::table('risk_scores', function (Blueprint $table) {
            $table->dropForeign(['country_id']);
            $table->dropColumn([
                'country_id', 'weather_score', 'inflation_score',
                'exchange_score', 'news_score', 'total_score',
                'risk_level', 'calculated_at',
            ]);
        });
    }
};