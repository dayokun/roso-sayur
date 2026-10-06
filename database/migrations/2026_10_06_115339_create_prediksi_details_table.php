<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prediksi_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prediksi_id')->constrained('prediksi')->cascadeOnDelete();
            $table->unsignedTinyInteger('rule_id');
            $table->decimal('alpha_pesanan', 8, 4);
            $table->decimal('alpha_historis', 8, 4);
            $table->decimal('alpha_tren', 8, 4);
            $table->decimal('alpha_value', 8, 4);
            $table->string('output_term'); // SEDIKIT | SEDANG | BANYAK
            $table->decimal('z_value', 10, 4);
            $table->decimal('bobot', 12, 4);
            $table->timestamps();

            $table->index(['prediksi_id', 'rule_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prediksi_detail');
    }
};
