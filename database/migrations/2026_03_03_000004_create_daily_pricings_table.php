<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_pricings', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke tabel products
            $table->foreignUuid('product_id')->constrained('products')->cascadeOnDelete();
            
            // Tanggal pencatatan harga
            $table->date('date_input'); 

            // RAW DATA INPUT
            $table->decimal('hpp', 15, 2); 
            $table->decimal('c1', 15, 2)->nullable(); 
            $table->decimal('c2', 15, 2)->nullable(); 
            $table->decimal('c3', 15, 2)->nullable(); 
            $table->integer('stock'); 
            $table->enum('demand', ['tinggi', 'normal', 'rendah'])->default('normal'); 
            $table->decimal('hpp_prev', 15, 2)->nullable(); 
            
            // VARIABEL RUMUS
            $table->decimal('buffer_percent', 5, 2)->default(0.05); 
            $table->decimal('markup_base', 5, 2)->default(0.20); 
            $table->integer('threshold_stock')->default(20); 
            
            // OUTPUT KALKULASI
            $table->decimal('final_price', 15, 2)->nullable(); 
            $table->decimal('margin_percent', 5, 2)->nullable(); 
            $table->string('status_margin')->nullable(); 
            
            // Audit Trail Kontrak Aktif
            $table->foreignId('supplier_contract_id')->nullable()->constrained('supplier_contracts')->nullOnDelete();
            $table->integer('active_contract_version')->nullable(); 

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_pricings');
    }
};