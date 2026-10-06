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
            $this->buatPesananHarian(Carbon::today()->subDays($d), $konsumens, $produks, true);
        }

        // Hari ini: sebagian pending, sebagian diterima
        $this->buatPesananHarian(Carbon::today(), $konsumens, $produks, false);
    }

    private function buatPesananHarian(Carbon $tanggal, $konsumens, $produks, bool $selesai): void
    {
        $konsumenList = $konsumens->shuffle()->values();
        $pesanans = []; // konsumen_id => Pesanan

        $buatDetail = function (Konsumen $konsumen, Produk $produk) use ($tanggal, $selesai, &$pesanans) {
            if (! isset($pesanans[$konsumen->id])) {
                $pesanans[$konsumen->id] = Pesanan::create([
                    'konsumen_id' => $konsumen->id,
                    'tgl_pesan' => $tanggal->copy()->setTime(rand(8, 17), rand(0, 59)),
                    'tgl_ambil' => $tanggal->copy()->addDay()->toDateString(),
                    'input_source' => fake()->randomElement(['bot_consumer', 'bot_consumer', 'dashboard']),
                    'status' => $selesai
                        ? Pesanan::STATUS_SELESAI
                        : fake()->randomElement([Pesanan::STATUS_PENDING, Pesanan::STATUS_DITERIMA]),
                    'is_late_order' => false,
                ]);
            }
            $pesanan = $pesanans[$konsumen->id];

            if ($pesanan->details()->where('produk_id', $produk->id)->exists()) {
                return; // hindari duplikat produk dalam 1 pesanan
            }

            $qty = round(rand(10, 100) / 10, 1);

            $delivered = null;
            $selisih = null;
            if ($selesai) {
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
        };

        // 1. Setiap produk pasti dipesan minimal 1x hari ini (toko sayur realistis)
        $i = 0;
        foreach ($produks as $produk) {
            $buatDetail($konsumenList[$i % $konsumenList->count()], $produk);
            $i++;
        }

        // 2. Variasi tambahan: pesanan ekstra acak
        for ($k = 0; $k < rand(10, 20); $k++) {
            $buatDetail($konsumenList->random(), $produks->random());
        }
    }
}
