<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prediksi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produk_id')->constrained('produk')->cascadeOnDelete();
            $table->date('tgl_prediksi');
            $table->decimal('qty_rekomendasi', 10, 2);
            $table->decimal('qty_dengan_buffer', 10, 2);
            $table->decimal('input_pesanan', 10, 2);
            $table->decimal('input_historis', 10, 2);
            $table->decimal('input_tren', 10, 4);
            $table->foreignId('mf_config_version_id')->nullable()->constrained('produk_mf_config')->nullOnDelete();
            $table->timestamps();

            $table->unique(['produk_id', 'tgl_prediksi']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prediksi');
    }
};
