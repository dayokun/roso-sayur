<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pesanan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('konsumen_id')->constrained('konsumen')->cascadeOnDelete();
            $table->dateTime('tgl_pesan');
            $table->date('tgl_ambil');
            $table->string('input_source')->default('dashboard');
            $table->enum('status', ['pending', 'diterima', 'siap_diambil', 'selesai', 'cancelled'])->default('pending');
            $table->boolean('is_late_order')->default(false);
            $table->string('late_order_status')->nullable();
            $table->string('late_order_rejection_reason')->nullable();
            $table->dateTime('notified_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pesanan');
    }
};
