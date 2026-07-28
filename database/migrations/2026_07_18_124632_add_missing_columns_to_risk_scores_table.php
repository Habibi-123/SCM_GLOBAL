<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('risk_scores', function (Blueprint $table) {
            if (!Schema::hasColumn('risk_scores', 'country_id')) {
                $table->foreignId('country_id')->after('id')->constrained()->cascadeOnDelete();
            }
            if (!Schema::hasColumn('risk_scores', 'weather_score')) {
                $table->decimal('weather_score', 5, 2)->default(0)->after('country_id');
            }
            if (!Schema::hasColumn('risk_scores', 'inflation_score')) {
                $table->decimal('inflation_score', 5, 2)->default(0)->after('weather_score');
            }
            if (!Schema::hasColumn('risk_scores', 'exchange_score')) {
                $table->decimal('exchange_score', 5, 2)->default(0)->after('inflation_score');
            }
            if (!Schema::hasColumn('risk_scores', 'news_score')) {
                $table->decimal('news_score', 5, 2)->default(0)->after('exchange_score');
            }
            if (!Schema::hasColumn('risk_scores', 'total_score')) {
                $table->decimal('total_score', 5, 2)->default(0)->after('news_score');
            }
            if (!Schema::hasColumn('risk_scores', 'risk_level')) {
                $table->enum('risk_level', ['low', 'medium', 'high'])->default('low')->after('total_score');
            }
            if (!Schema::hasColumn('risk_scores', 'calculated_at')) {
                $table->timestamp('calculated_at')->nullable()->after('risk_level');
            }
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