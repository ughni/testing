<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('supplier_offers', function (Blueprint $table) {
            // Nambahin unit setelah qty
            $table->string('unit', 50)->default('Pcs')->after('qty');
        });
    }

    public function down()
    {
        Schema::table('supplier_offers', function (Blueprint $table) {
            $table->dropColumn('unit');
        });
    }
};