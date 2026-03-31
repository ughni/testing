<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('adjustment_rules', function (Blueprint $table) {
        $table->id();
        // 1. Aturan HPP Naik
        $table->decimal('hpp_increase_threshold', 5, 4)->default(0.0300); // Batas HPP dibilang naik (3%)
        $table->decimal('hpp_adjustment', 5, 4)->default(0.0300);         // Harga ditambah (3%)
        
        // 2. Aturan Demand (Permintaan) Pasar
        $table->decimal('demand_high_adjustment', 5, 4)->default(0.0300); // Kalau viral, harga ditambah (3%)
        $table->decimal('demand_low_adjustment', 5, 4)->default(-0.0300); // Kalau sepi, harga didiskon (-3%)
        
        // 3. Aturan Stok Menipis
        $table->decimal('stock_low_adjustment', 5, 4)->default(0.0200);   // Kalau stok < threshold, harga ditambah (2%)
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('adjustment_rules');
    }
};
