<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            // Tambahkan kolom-kolom ini agar sesuai dengan Seeder kamu
            $table->decimal('markup_base', 5, 2)->default(20.00);
            $table->decimal('markup_demand_high', 5, 2)->default(15.00);
            $table->decimal('markup_demand_low', 5, 2)->default(10.00);
            $table->decimal('competitor_weight', 5, 2)->default(5.00);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};