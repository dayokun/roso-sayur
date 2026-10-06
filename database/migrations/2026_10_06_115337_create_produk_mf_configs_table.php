<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produk_mf_config', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produk_id')->constrained('produk')->cascadeOnDelete();
            $table->string('variabel'); // pesanan | historis (tren dihitung dinamis)
            $table->decimal('batas_bawah', 10, 2);  // a = P33
            $table->decimal('batas_tengah', 10, 2); // b = P66
            $table->decimal('batas_atas', 10, 2);   // c = P95
            $table->string('generated_from')->nullable();
            $table->date('valid_from');
            $table->date('valid_until')->nullable();
            $table->dateTime('generated_at');
            $table->timestamps();

            $table->index(['produk_id', 'variabel', 'valid_from']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produk_mf_config');
    }
};
