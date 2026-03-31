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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action'); // Cth: 'UPDATE', 'DELETE', 'CREATE'
            $table->string('module'); // Cth: 'Master Produk', 'Pricing Engine'
            $table->text('description'); // Cth: 'Mengubah HPP Kulkas dari 1jt jadi 1.2jt'
            $table->string('ip_address')->nullable();
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
