<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            // Menjawab kebutuhan product_id UUID dari dokumen
            $table->uuid('id')->primary(); 
            
            // 👇🔥 YANG DIPERBAIKI: Ganti cascadeOnDelete jadi nullOnDelete 🔥👇
            // Relasi ke tabel suppliers (FK supplier)
            $table->foreignUuid('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            
            // Nama produk
            $table->string('product_name');

            // 👇 INI 4 BARIS TAMBAHAN DARI REQUEST WA KLIEN 👇
            $table->string('category'); // Kategori produk
            $table->string('unit'); // Satuan beli (kg, pcs, liter)
            $table->text('description')->nullable(); // Keterangan produk (boleh kosong)
            $table->boolean('is_active')->default(true); // Aktif produk (default: true/aktif)
            // 👆 ========================================= 👆
            
            // Tipe Harga (dynamic / consignment / HET)
            $table->enum('price_type', ['dynamic', 'consignment', 'HET'])->default('dynamic');
            
            // Variabel khusus sesuai mode perhitungan
            $table->decimal('het_price', 15, 2)->nullable(); // Harga eceran tertinggi
            $table->decimal('consignment_margin', 5, 2)->nullable(); // Margin wajib dari supplier
            $table->decimal('selling_price_fixed', 15, 2)->nullable(); // Harga jual wajib dari kontrak
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};