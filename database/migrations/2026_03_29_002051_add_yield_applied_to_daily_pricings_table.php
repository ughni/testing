<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('daily_pricings', function (Blueprint $table) {
            // Bikin laci baru buat nyimpen angka Yield Harian
            $table->decimal('yield_applied', 5, 2)->nullable()->after('c3');
        });
    }

    public function down()
    {
        Schema::table('daily_pricings', function (Blueprint $table) {
            $table->dropColumn('yield_applied');
        });
    }
};