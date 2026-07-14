<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ports', function (Blueprint $table) {
            $table->foreignId('country_id')->after('id')->constrained()->cascadeOnDelete();
            $table->string('name')->after('country_id');
            $table->string('unlocode', 10)->nullable()->after('name');
            $table->decimal('latitude', 10, 7)->after('unlocode');
            $table->decimal('longitude', 10, 7)->after('latitude');

            $table->index('name');
        });
    }

    public function down(): void
    {
        Schema::table('ports', function (Blueprint $table) {
            $table->dropIndex(['name']);
            $table->dropForeign(['country_id']);
            $table->dropColumn(['country_id', 'name', 'unlocode', 'latitude', 'longitude']);
        });
    }
};