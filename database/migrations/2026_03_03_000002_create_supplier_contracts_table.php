<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supplier_contracts', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke tabel suppliers (Wajib UUID sesuai dokumen)
            $table->foreignUuid('supplier_id')->constrained('suppliers')->cascadeOnDelete();
            
            // Variabel Upload Dokumen Kontrak dari klien
            $table->string('contract_file'); // pdf/jpg
            $table->integer('contract_version'); // versi dokumen
            $table->string('contract_price_rule')->nullable(); // aturan harga di kontrak
            $table->decimal('contract_margin', 5, 2)->nullable(); // persentase margin
            $table->decimal('contract_het_price', 15, 2)->nullable(); // harga HET
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_contracts');
    }
};