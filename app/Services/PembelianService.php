<?php

namespace App\Services;

use App\Models\Pembelian;
use App\Models\PembelianDetail;
use App\Models\Pesanan;
use App\Models\Prediksi;
use Carbon\Carbon;

/**
 * Pencatatan actual pembelian oleh staff (PRD 6.8).
 */
class PembelianService
{
    /**
     * Simpan actual pembelian per produk untuk satu tanggal.
     *
     * @param  array<int, array{produk_id: int, qty_beli: float, harga_beli: ?float, is_available_today: bool}>  $items
     */
    public function simpanActual(Carbon $tgl, array $items, ?string $catatan = null): Pembelian
    {
        $pembelian = Pembelian::updateOrCreate(
            ['tgl_beli' => $tgl->toDateString()],
            ['catatan' => $catatan]
        );

        foreach ($items as $item) {
            $prediksi = Prediksi::where('produk_id', $item['produk_id'])
                ->whereDate('tgl_prediksi', $tgl->toDateString())
                ->first();

            PembelianDetail::updateOrCreate(
                ['pembelian_id' => $pembelian->id, 'produk_id' => $item['produk_id']],
                [
                    'prediksi_id' => $prediksi?->id,
                    'is_available_today' => $item['is_available_today'] ?? true,
                    'qty_beli' => ($item['is_available_today'] ?? true) ? (float) ($item['qty_beli'] ?? 0) : 0,
                    'harga_beli' => $item['harga_beli'] ?? null,
                ]
            );
        }

        return $pembelian->load('details');
    }

    /**
     * Cek produk yang ada pesanannya tapi belum diinput actual belinya.
     *
     * @return array<int, string>  [produk_id => nama_produk]
     */
    public function belumDiinput(Carbon $tgl): array
    {
        $tglStr = $tgl->toDateString();

        $produkPesan = \App\Models\PesananDetail::query()
            ->join('pesanan', 'pesanan.id', '=', 'pesanan_detail.pesanan_id')
            ->join('produk', 'produk.id', '=', 'pesanan_detail.produk_id')
            ->whereDate('pesanan.tgl_ambil', $tglStr)
            ->where('pesanan.status', '!=', Pesanan::STATUS_CANCELLED)
            ->distinct()
            ->pluck('produk.nama', 'produk.id')
            ->toArray();

        $sudah = PembelianDetail::query()
            ->join('pembelian', 'pembelian.id', '=', 'pembelian_detail.pembelian_id')
            ->whereDate('pembelian.tgl_beli', $tglStr)
            ->pluck('pembelian_detail.produk_id')
            ->toArray();

        return array_diff_key($produkPesan, array_flip($sudah));
    }
}
