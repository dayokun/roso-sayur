<?php

namespace App\Services;

use App\Models\EvaluasiPrediksi;
use App\Models\Pesanan;
use App\Models\PesananDetail;
use App\Models\Prediksi;
use Carbon\Carbon;

/**
 * Evaluasi akurasi prediksi (PRD 6.10.3).
 * Menggunakan qty_pesan sebagai proxy actual demand (bukan qty_delivered).
 */
class EvaluasiService
{
    /**
     * Hitung MAPE & MAE untuk semua prediksi pada tanggal tertentu.
     *
     * @return array{dievaluasi: int}
     */
    public function hitung(Carbon $tgl): array
    {
        $tglStr = $tgl->toDateString();
        $n = 0;

        $prediksis = Prediksi::whereDate('tgl_prediksi', $tglStr)->get();

        foreach ($prediksis as $prediksi) {
            $aktual = (float) PesananDetail::query()
                ->join('pesanan', 'pesanan.id', '=', 'pesanan_detail.pesanan_id')
                ->where('pesanan_detail.produk_id', $prediksi->produk_id)
                ->where('pesanan_detail.is_anomali', false)
                ->where('pesanan.is_late_order', false)
                ->whereDate('pesanan.tgl_ambil', $tglStr)
                ->where('pesanan.status', '!=', Pesanan::STATUS_CANCELLED)
                ->sum('pesanan_detail.qty_pesan');

            $pred = (float) $prediksi->qty_rekomendasi;
            $mape = $aktual > 0 ? abs($aktual - $pred) / $aktual * 100 : null;
            $mae = abs($aktual - $pred);

            EvaluasiPrediksi::updateOrCreate(
                ['prediksi_id' => $prediksi->id],
                ['qty_aktual_demand' => $aktual, 'mape' => $mape, 'mae' => $mae]
            );
            $n++;
        }

        return ['dievaluasi' => $n];
    }

    /**
     * Ringkasan metrik agregat per produk dalam rentang tanggal.
     */
    public function ringkasan(Carbon $dari, Carbon $sampai): array
    {
        $rows = EvaluasiPrediksi::query()
            ->join('prediksi', 'prediksi.id', '=', 'evaluasi_prediksi.prediksi_id')
            ->join('produk', 'produk.id', '=', 'prediksi.produk_id')
            ->whereDate('prediksi.tgl_prediksi', '>=', $dari->toDateString())
            ->whereDate('prediksi.tgl_prediksi', '<=', $sampai->toDateString())
            ->groupBy('produk.id', 'produk.nama')
            ->selectRaw('produk.nama,
                COUNT(*) as n,
                AVG(evaluasi_prediksi.mape) as avg_mape,
                AVG(evaluasi_prediksi.mae) as avg_mae')
            ->get();

        // Waste rate & fulfillment per produk
        $waste = PesananDetail::query()
            ->join('pesanan', 'pesanan.id', '=', 'pesanan_detail.pesanan_id')
            ->join('produk', 'produk.id', '=', 'pesanan_detail.produk_id')
            ->leftJoin('pembelian_detail', 'pembelian_detail.produk_id', '=', 'produk.id')
            ->leftJoin('pembelian', function ($j) {
                $j->on('pembelian.id', '=', 'pembelian_detail.pembelian_id');
            })
            ->whereDate('pesanan.tgl_ambil', '>=', $dari->toDateString())
            ->whereDate('pesanan.tgl_ambil', '<=', $sampai->toDateString())
            ->where('pesanan.status', Pesanan::STATUS_SELESAI)
            ->groupBy('produk.id', 'produk.nama')
            ->selectRaw('produk.nama,
                SUM(pesanan_detail.qty_pesan) as total_pesan,
                SUM(pesanan_detail.qty_delivered) as total_delivered')
            ->get()
            ->keyBy('nama');

        return [
            'akurasi' => $rows,
            'fulfillment' => $waste,
        ];
    }
}
