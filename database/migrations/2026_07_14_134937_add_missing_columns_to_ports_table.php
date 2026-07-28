<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ports', function (Blueprint $table) {
            if (!Schema::hasColumn('ports', 'country_id')) {
                $table->foreignId('country_id')->after('id')->constrained()->cascadeOnDelete();
            }
            if (!Schema::hasColumn('ports', 'name')) {
                $table->string('name')->after('country_id');
                $table->index('name');
            }
            if (!Schema::hasColumn('ports', 'unlocode')) {
                $table->string('unlocode', 10)->nullable()->after('name');
            }
            if (!Schema::hasColumn('ports', 'latitude')) {
                $table->decimal('latitude', 10, 7)->after('unlocode');
            }
            if (!Schema::hasColumn('ports', 'longitude')) {
                $table->decimal('longitude', 10, 7)->after('latitude');
            }
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