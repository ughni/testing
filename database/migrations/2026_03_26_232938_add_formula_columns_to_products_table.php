<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            // Kita bikin 4 kolom ini NULLABLE (Opsional)
            // Kalau misal tabel lu nggak punya kolom 'unit', hapus aja tulisan ->after('unit') nya ya Breyy.
            $table->decimal('buffer_percent', 5, 4)->nullable()->after('unit');
            $table->decimal('markup_base', 5, 4)->nullable()->after('buffer_percent');
            $table->integer('threshold_stock')->nullable()->after('markup_base');
            $table->decimal('yield_percent', 5, 4)->nullable()->after('threshold_stock');
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            // Buat ngehapus kolom kalau misal mau di-rollback
            $table->dropColumn(['buffer_percent', 'markup_base', 'threshold_stock', 'yield_percent']);
        });
    }
};