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
    Schema::create('formula_settings', function (Blueprint $table) {
        $table->id();
        $table->decimal('buffer_percent', 5, 4)->default(0.0500); // nyimpen 0.05
        $table->decimal('markup_base', 5, 4)->default(0.2000);    // nyimpen 0.20
        $table->integer('threshold_stock')->default(20);          // nyimpen 20
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('formula_settings');
    }
};
