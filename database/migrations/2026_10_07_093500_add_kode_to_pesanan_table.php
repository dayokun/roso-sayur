<?php

use App\Models\Pesanan;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Kode pesanan unik per hari, mis. RS-20261007-0003.
     * Bisa dibagikan ke customer sebagai referensi pengambilan.
     */
    public function up(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            $table->string('kode', 20)->nullable()->unique()->after('id');
        });

        // Backfill untuk pesanan yang sudah ada
        Pesanan::orderBy('id')->each(function (Pesanan $p) {
            $p->kode = Pesanan::buatKode($p->tgl_pesan);
            $p->saveQuietly();
        });
    }

    public function down(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            $table->dropColumn('kode');
        });
    }
};
