<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            // Gue biarin pakai UUID sesuai bawaan lu
            $table->uuid('id')->primary();
            
            // 👇🔥 YANG DITAMBAHKAN (KTP SUPPLIER SESUAI BOS YUDHI) 🔥👇
            $table->string('no_supplier')->unique(); // Misal: SUP-001
            $table->string('nama_supplier'); // Ganti supplier_name jadi ini biar seragam sama Bos
            $table->enum('kualifikasi', ['produsen', 'distributor', 'agen', 'retail', 'pasar']); 
            $table->text('alamat')->nullable(); 
            $table->string('kontak_person')->nullable(); 
            $table->string('email')->nullable(); 
            $table->boolean('is_active')->default(true); // Status aktif/deaktif
            $table->boolean('is_contract')->default(false); // Status kontrak/non kontrak
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};