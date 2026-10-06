<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bot_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('no_hp')->unique();
            $table->string('nama')->nullable();
            $table->foreignId('konsumen_id')->nullable()->constrained('konsumen')->nullOnDelete();
            $table->string('state')->default('idle'); // idle|nama|produk|qty|tambah|tgl_ambil|konfirmasi
            $table->json('data')->nullable(); // keranjang sementara: [{produk_id, qty}]
            $table->unsignedTinyInteger('percobaan')->default(0);
            $table->dateTime('last_activity_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bot_sessions');
    }
};
