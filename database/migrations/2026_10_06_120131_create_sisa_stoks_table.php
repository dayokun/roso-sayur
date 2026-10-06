<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sisa_stok', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembelian_detail_id')->constrained('pembelian_detail')->cascadeOnDelete();
            $table->date('tgl');
            $table->decimal('qty_sisa', 10, 2)->default(0);
            $table->enum('status_sisa', ['dijual_murah', 'dibuang'])->nullable();
            $table->decimal('harga_jual_murah', 12, 2)->nullable();
            $table->decimal('qty_terjual_murah', 10, 2)->nullable();
            $table->decimal('qty_dibuang', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sisa_stok');
    }
};
