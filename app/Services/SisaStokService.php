<?php

namespace App\Services;

use App\Models\PembelianDetail;
use App\Models\Pesanan;
use App\Models\PesananDetail;
use App\Models\SisaStok;
use Carbon\Carbon;

/**
 * Perhitungan sisa stok otomatis (PRD 6.9):
 * qty_sisa = qty_beli - total qty_delivered hari itu.
 */
class SisaStokService
{
    /**
     * Dipanggil setiap kali staff menginput qty_delivered sebuah detail pesanan.
     */
    public function catatDelivered(PesananDetail $detail): void
    {
        $pesanan = $detail->pesanan;
        $tgl = Carbon::parse($pesanan->tgl_ambil)->toDateString();

        $pembelianDetail = PembelianDetail::query()
            ->join('pembelian', 'pembelian.id', '=', 'pembelian_detail.pembelian_id')
            ->whereDate('pembelian.tgl_beli', $tgl)
            ->where('pembelian_detail.produk_id', $detail->produk_id)
            ->select('pembelian_detail.*')
            ->first();

        if (! $pembelianDetail) {
            return; // belum ada data pembelian hari itu
        }

        $totalDelivered = (float) PesananDetail::query()
            ->join('pesanan', 'pesanan.id', '=', 'pesanan_detail.pesanan_id')
            ->where('pesanan_detail.produk_id', $detail->produk_id)
            ->whereDate('pesanan.tgl_ambil', $tgl)
            ->where('pesanan.status', Pesanan::STATUS_SELESAI)
            ->sum('pesanan_detail.qty_delivered');

        $qtySisa = max(0, (float) $pembelianDetail->qty_beli - $totalDelivered);

        SisaStok::updateOrCreate(
            ['pembelian_detail_id' => $pembelianDetail->id, 'tgl' => $tgl],
            ['qty_sisa' => $qtySisa]
        );
    }

    /**
     * Input disposisi sisa stok oleh staff.
     */
    public function disposisi(SisaStok $sisa, string $status, ?float $harga = null, ?float $qtyJual = null, ?float $qtyBuang = null): SisaStok
    {
        $sisa->update([
            'status_sisa' => $status,
            'harga_jual_murah' => $harga,
            'qty_terjual_murah' => $qtyJual,
            'qty_dibuang' => $qtyBuang,
        ]);

        return $sisa->fresh();
    }

    /**
     * Daftar sisa stok per tanggal untuk dashboard.
     */
    public function daftar(Carbon $tgl): \Illuminate\Database\Eloquent\Collection
    {
        return SisaStok::with(['pembelianDetail.produk'])
            ->whereDate('tgl', $tgl->toDateString())
            ->get();
    }
}
