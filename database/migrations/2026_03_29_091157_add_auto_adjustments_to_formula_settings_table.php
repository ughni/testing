<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up(): void
    {
        Schema::table('formula_settings', function (Blueprint $table) {
            $table->decimal('hpp_naik_threshold', 8, 2)->nullable()->default(3); // Batas HPP naik (Contoh: 3%)
            $table->decimal('hpp_naik_action', 8, 2)->nullable()->default(3);    // Aksi harga naik (Contoh: 3%)
            $table->decimal('demand_tinggi', 8, 2)->nullable()->default(3);      // Viral naik (Contoh: 3%)
            $table->decimal('demand_rendah', 8, 2)->nullable()->default(3);      // Sepi turun (Contoh: 3%)
            $table->decimal('stok_menipis', 8, 2)->nullable()->default(2);       // Stok sekarat naik (Contoh: 2%)
        });
    }

    public function down(): void
    {
        Schema::table('formula_settings', function (Blueprint $table) {
            $table->dropColumn([
                'hpp_naik_threshold', 
                'hpp_naik_action', 
                'demand_tinggi', 
                'demand_rendah', 
                'stok_menipis'
            ]);
        });
    }
};
