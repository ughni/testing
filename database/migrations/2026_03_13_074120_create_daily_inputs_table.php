<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_inputs', function (Blueprint $table) {
            $table->id();
            
            // PERBAIKAN FATAL: Wajib pakai foreignUuid karena tabel products pakai UUID
            $table->foreignUuid('product_id')->constrained('products')->cascadeOnDelete();
            
            // Tanggal pencatatan harga
            $table->date('input_date');

            // HPP dan Harga Kompetitor (C1, C2, C3)
            // Pakai unsignedInteger karena harga (Rupiah) tidak mungkin minus
            $table->unsignedInteger('hpp');
            $table->unsignedInteger('c1')->nullable(); 
            $table->unsignedInteger('c2')->nullable();
            $table->unsignedInteger('c3')->nullable();

            // Stok barang
            $table->unsignedInteger('stock');

            // Demand: HARUS ENUM. 
            $table->enum('demand', ['tinggi', 'normal', 'rendah'])->default('normal');

            // PERBAIKAN: Kolom hpp_prev wajib ada untuk diisi otomatis oleh sistem nanti
            $table->unsignedInteger('hpp_prev')->nullable();

            // Penting untuk fitur Audit Trail (melacak siapa yang input) 
            $table->foreignId('user_id')->constrained('users');

            $table->timestamps();
            
            // Mencegah input ganda untuk produk yang sama di hari yang sama
            $table->unique(['product_id', 'input_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_inputs');
    }
};