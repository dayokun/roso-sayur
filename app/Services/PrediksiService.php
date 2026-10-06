<?php

namespace App\Services;

use App\Models\PesananDetail;
use App\Models\Prediksi;
use App\Models\Produk;
use App\Models\ProdukMfConfig;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Orkestrasi prediksi harian sesuai PRD Section 9.2 (Alur Prediksi).
 */
class PrediksiService
{
    public function __construct(
        protected FuzzyTsukamoto $fuzzy = new FuzzyTsukamoto(),
        protected MfConfigService $mfService = new MfConfigService(),
    ) {}

    /**
     * Jalankan prediksi untuk semua produk aktif pada tanggal target.
     *
     * @return array{produk: array, dilewati: array}
     */
    public function jalankan(Carbon $tglTarget): array
    {
        $hasil = ['produk' => [], 'dilewati' => []];

        $produks = Produk::where('is_available', true)->get();

        foreach ($produks as $produk) {
            $r = $this->prediksiProduk($produk, $tglTarget);
            if ($r['status'] === 'ok') {
                $hasil['produk'][] = $r;
            } else {
                $hasil['dilewati'][] = $r;
            }
        }

        return $hasil;
    }

    /**
     * Prediksi satu produk. Mengembalikan array hasil + status.
     */
    public function prediksiProduk(Produk $produk, Carbon $tglTarget): array
    {
        $tglStr = $tglTarget->toDateString();

        // Produk seasonal: sum of orders, tanpa fuzzy & tanpa buffer (PRD 6.7)
        if ($produk->is_seasonal) {
            $total = $this->totalPesanan($produk->id, $tglStr);

            $prediksi = $this->simpan($produk, $tglStr, [
                'qty_rekomendasi' => $total,
                'qty_dengan_buffer' => $total,
                'input_pesanan' => $total,
                'input_historis' => 0,
                'input_tren' => 0,
            ], [], null);

            return [
                'status' => 'ok',
                'produk' => $produk->nama,
                'metode' => 'seasonal_sum',
                'qty_rekomendasi' => $total,
                'qty_dengan_buffer' => $total,
                'prediksi_id' => $prediksi->id,
            ];
        }

        // --- Kumpulkan input ---
        $inputPesanan = $this->totalPesanan($produk->id, $tglStr);
        $harian = MfConfigService::historiHarian($produk->id, 7);
        $avg7 = $this->rataRata($harian);

        // Outlier capping / winsorizing 1.5x (PRD 6.6.5)
        $dicap = false;
        if ($avg7 > 0 && $inputPesanan > 1.5 * $avg7) {
            $inputPesanan = round(1.5 * $avg7, 2);
            $dicap = true;
        }

        // Tren: rata-rata 3 hari terakhir vs 4 hari sebelumnya
        $tgls = array_keys($harian);
        sort($tgls);
        $last3 = array_slice($tgls, -3);
        $prev4 = array_slice($tgls, -7, 4);
        $avg3 = $this->rataRata(array_intersect_key($harian, array_flip($last3)));
        $avgPrev = $this->rataRata(array_intersect_key($harian, array_flip($prev4)));
        $deltaRatio = $avgPrev > 0 ? ($avg3 - $avgPrev) / $avgPrev : 0.0;

        // --- Pre-check zero order (PRD 6.6.5) ---
        $hariAdaPesanan = count(array_filter($harian, fn ($v) => $v > 0));
        if ($inputPesanan == 0 && $hariAdaPesanan <= 2) {
            return $this->skip($produk, 'zero_order_rendah', 'Pesanan 0 dan historis rendah — rekomendasi 0, tidak perlu beli');
        }
        $butuhPerhatian = $inputPesanan == 0 && $hariAdaPesanan >= 5;

        // --- MF config ---
        $mfP = ProdukMfConfig::aktif($produk->id, ProdukMfConfig::VARIABEL_PESANAN, $tglStr);
        $mfH = ProdukMfConfig::aktif($produk->id, ProdukMfConfig::VARIABEL_HISTORIS, $tglStr);
        if (! $mfP || ! $mfH) {
            return $this->skip($produk, 'mf_tidak_ada', 'Konfigurasi MF tidak ditemukan — prediksi di-skip, alert ke admin');
        }

        // --- Inferensi ---
        $hasil = $this->fuzzy->infer(
            $inputPesanan,
            $avg7,
            $deltaRatio,
            ['a' => (float) $mfP->batas_bawah, 'b' => (float) $mfP->batas_tengah, 'c' => (float) $mfP->batas_atas],
            ['a' => (float) $mfH->batas_bawah, 'b' => (float) $mfH->batas_tengah, 'c' => (float) $mfH->batas_atas],
        );

        // --- Safety buffer per kategori (maks 15%) ---
        $buffer = (float) ($produk->kategori->safety_buffer_persen ?? 0);
        $buffer = min($buffer, 15.0);
        $qtyBuffer = (int) ceil($hasil['qty_rekomendasi'] * (1 + $buffer / 100));

        $prediksi = $this->simpan($produk, $tglStr, [
            'qty_rekomendasi' => $hasil['qty_rekomendasi'],
            'qty_dengan_buffer' => $qtyBuffer,
            'input_pesanan' => $inputPesanan,
            'input_historis' => round($avg7, 2),
            'input_tren' => round($deltaRatio, 4),
        ], $hasil['rules'], $mfP->id);

        return [
            'status' => 'ok',
            'produk' => $produk->nama,
            'metode' => 'fuzzy_tsukamoto',
            'qty_rekomendasi' => $hasil['qty_rekomendasi'],
            'qty_dengan_buffer' => $qtyBuffer,
            'buffer_persen' => $buffer,
            'outlier_dicap' => $dicap,
            'butuh_perhatian' => $butuhPerhatian,
            'rules_aktif' => count($hasil['rules']),
            'prediksi_id' => $prediksi->id,
        ];
    }

    /**
     * Total qty_pesan untuk tgl_ambil tertentu (kecualikan anomali).
     */
    protected function totalPesanan(int $produkId, string $tglAmbil): float
    {
        return (float) PesananDetail::query()
            ->join('pesanan', 'pesanan.id', '=', 'pesanan_detail.pesanan_id')
            ->where('pesanan_detail.produk_id', $produkId)
            ->where('pesanan_detail.is_anomali', false)
            ->whereDate('pesanan.tgl_ambil', $tglAmbil)
            ->sum('pesanan_detail.qty_pesan');
    }

    protected function rataRata(array $harian): float
    {
        if (empty($harian)) {
            return 0.0;
        }

        return array_sum($harian) / count($harian);
    }

    protected function simpan(Produk $produk, string $tgl, array $data, array $rules, ?int $mfId): Prediksi
    {
        $prediksi = Prediksi::updateOrCreate(
            ['produk_id' => $produk->id, 'tgl_prediksi' => $tgl],
            [...$data, 'mf_config_version_id' => $mfId],
        );

        // Tulis ulang log transparansi
        $prediksi->details()->delete();
        foreach ($rules as $r) {
            $prediksi->details()->create([...$r, 'prediksi_id' => $prediksi->id]);
        }

        return $prediksi;
    }

    protected function skip(Produk $produk, string $kode, string $alasan): array
    {
        return [
            'status' => 'dilewati',
            'produk' => $produk->nama,
            'kode' => $kode,
            'alasan' => $alasan,
        ];
    }

    /**
     * Daftar hasil prediksi per tanggal untuk dashboard,
     * lengkap dengan rincian aturan aktif (transparansi fuzzy).
     */
    public function daftarHasil(Carbon $tgl): \Illuminate\Database\Eloquent\Collection
    {
        return Prediksi::with(['produk.kategori', 'details'])
            ->whereDate('tgl_prediksi', $tgl->toDateString())
            ->orderBy('id')
            ->get();
    }
}
