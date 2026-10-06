<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluasi_prediksi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prediksi_id')->unique()->constrained('prediksi')->cascadeOnDelete();
            $table->decimal('qty_aktual_demand', 10, 2)->nullable();
            $table->decimal('mape', 8, 4)->nullable();
            $table->decimal('mae', 10, 4)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluasi_prediksi');
    }
};
