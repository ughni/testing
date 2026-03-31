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
    Schema::create('api_credentials', function (Blueprint $table) {
        $table->id();
        $table->string('app_name'); // Nama aplikasi yang mau nyambung (Misal: POS Kasir Depan)
        $table->string('api_key')->unique(); // Token rahasianya
        $table->boolean('is_active')->default(true); // Status nyala/mati
        $table->timestamp('last_used_at')->nullable(); // Kapan terakhir kali API ini dipanggil
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_credentials');
    }
};
