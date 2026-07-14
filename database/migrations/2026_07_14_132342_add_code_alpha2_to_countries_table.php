<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('countries', function (Blueprint $table) {
            // Dibutuhkan untuk mencocokkan data pelabuhan (World Port Index)
            // yang pakai format ISO Alpha-2, bukan Alpha-3 seperti kolom `code` kita
            $table->string('code_alpha2', 2)->nullable()->after('code');
        });
    }

    public function down(): void
    {
        Schema::table('countries', function (Blueprint $table) {
            $table->dropColumn('code_alpha2');
        });
    }
};