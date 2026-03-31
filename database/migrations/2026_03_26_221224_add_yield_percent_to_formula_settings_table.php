<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('formula_settings', function (Blueprint $table) {
            // Nambahin yield_percent dengan default 1.0000 (Artinya 100%)
            $table->decimal('yield_percent', 5, 4)->default(1.0000)->after('threshold_stock');
        });
    }

    public function down()
    {
        Schema::table('formula_settings', function (Blueprint $table) {
            $table->dropColumn('yield_percent');
        });
    }
};