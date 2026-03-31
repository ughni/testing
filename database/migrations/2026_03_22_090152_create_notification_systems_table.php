<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('notification_systems', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // Contoh: 'danger', 'warning', 'info', 'success'
            $table->string('title'); // Judul notif
            $table->text('message'); // Isi pesannya
            $table->string('icon'); // Contoh: 'fas fa-exclamation-triangle'
            $table->boolean('is_read')->default(false); // Buat nandain udah dibaca belum
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('notification_systems');
    }
};