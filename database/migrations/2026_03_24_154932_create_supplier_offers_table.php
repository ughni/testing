<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supplier_offers', function (Blueprint $table) {
            $table->uuid('id')->primary(); // Pakai UUID biar seragam sama tabel lu yang lain
            $table->string('supplier_name');
            $table->string('product_name');
            $table->decimal('price', 15, 2)->default(0);
            $table->string('status')->default('pending'); // pending, approved, rejected, hold
            $table->integer('qty')->nullable();
            $table->date('offer_date')->nullable();
            $table->date('hold_until')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_offers');
    }
};
