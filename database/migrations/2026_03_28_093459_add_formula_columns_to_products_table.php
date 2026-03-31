<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Kita selipin 3 kolom ini tanpa ganggu data yang udah ada
            $table->decimal('markup', 5, 2)->default(20.00)->comment('Target Profit (contoh: 20%)')->after('is_active');
            $table->decimal('buffer', 5, 2)->default(5.00)->comment('Batas Aman Harga (contoh: 5%)')->after('markup');
            $table->integer('threshold')->default(20)->comment('Batas Kritis Sisa Stok')->after('buffer');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Kalau mau di-rollback (dibatalkan), hapus 3 kolom ini aja
            $table->dropColumn(['markup', 'buffer', 'threshold']);
        });
    }
};