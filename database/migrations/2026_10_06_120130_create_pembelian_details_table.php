<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembelian_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembelian_id')->constrained('pembelian')->cascadeOnDelete();
            $table->foreignId('produk_id')->constrained('produk')->cascadeOnDelete();
            $table->foreignId('prediksi_id')->nullable()->constrained('prediksi')->nullOnDelete();
            $table->boolean('is_available_today')->default(true);
            $table->decimal('qty_beli', 10, 2)->default(0);
            $table->decimal('harga_beli', 12, 2)->nullable();
            $table->timestamps();

            $table->unique(['pembelian_id', 'produk_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembelian_detail');
    }
};
