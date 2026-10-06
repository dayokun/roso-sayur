<?php

namespace Database\Seeders;

use App\Models\Konsumen;
use App\Models\Pesanan;
use App\Models\PesananDetail;
use App\Models\Produk;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PesananSeeder extends Seeder
{
    public function run(): void
    {
        $konsumens = Konsumen::all();
        $produks = Produk::where('is_available', true)->get();

        if ($konsumens->isEmpty() || $produks->isEmpty()) {
            return;
        }

        // 30 hari histori (H-30 s/d H-1): status selesai
        for ($d = 30; $d >= 1; $d--) {
            $tglPesan = Carbon::today()->subDays($d);
            $this->buatPesananHarian($tglPesan, $konsumens, $produks, true);
        }

        // Hari ini: sebagian pending, sebagian diterima
        $this->buatPesananHarian(Carbon::today(), $konsumens, $produks, false);
    }

    private function buatPesananHarian(Carbon $tanggal, $konsumens, $produks, bool $selesai): void
    {
        $jumlahPesanan = rand(5, 12);
        $pemesan = $konsumens->random(min($jumlahPesanan, $konsumens->count()));

        foreach ($pemesan as $konsumen) {
            $tglPesan = $tanggal->copy()->setTime(rand(8, 17), rand(0, 59));

            $pesanan = Pesanan::create([
                'konsumen_id' => $konsumen->id,
                'tgl_pesan' => $tglPesan,
                'tgl_ambil' => $tanggal->copy()->addDay()->toDateString(),
                'input_source' => fake()->randomElement(['bot_consumer', 'bot_consumer', 'dashboard']),
                'status' => $selesai ? Pesanan::STATUS_SELESAI : fake()->randomElement([Pesanan::STATUS_PENDING, Pesanan::STATUS_DITERIMA]),
                'is_late_order' => false,
            ]);

            $items = $produks->random(rand(1, 3));
            foreach ($items as $produk) {
                $qty = round(rand(10, 100) / 10, 1); // 1.0 - 10.0

                $delivered = null;
                $selisih = null;
                if ($selesai) {
                    // Sesekali delivered sedikit kurang dari pesan (realistis)
                    $delivered = fake()->boolean(85) ? $qty : round($qty * rand(80, 99) / 100, 1);
                    $selisih = round($delivered - $qty, 1);
                }

                PesananDetail::create([
                    'pesanan_id' => $pesanan->id,
                    'produk_id' => $produk->id,
                    'qty_pesan' => $qty,
                    'satuan' => $produk->satuan,
                    'qty_delivered' => $delivered,
                    'selisih' => $selisih,
                    'is_anomali' => false,
                ]);
            }
        }
    }
}
