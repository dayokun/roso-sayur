<?php

namespace App\Services;

use App\Models\Pesanan;
use App\Models\Prediksi;
use Carbon\Carbon;

/**
 * Ringkasan operasional harian untuk dashboard.
 */
class DashboardService
{
    public function ringkasan(): array
    {
        $hariIni = Carbon::today()->toDateString();
        $besok = Carbon::tomorrow()->toDateString();

        $pesananHariIni = Pesanan::whereDate('tgl_ambil', $hariIni)
            ->groupBy('status')
            ->selectRaw('status, COUNT(*) as jml')
            ->pluck('jml', 'status');

        $pesananBesok = Pesanan::whereDate('tgl_ambil', $besok)
            ->where('status', '!=', Pesanan::STATUS_CANCELLED)
            ->count();

        // Pesanan dengan tanggal ambil setelah hari ini — jebakan UX: halaman
        // Pesanan default memfilter hari ini, sehingga order masa depan tak terlihat.
        $pesananMendatang = Pesanan::with('konsumen')
            ->whereDate('tgl_ambil', '>', $hariIni)
            ->where('status', '!=', Pesanan::STATUS_CANCELLED)
            ->orderBy('tgl_ambil')
            ->orderBy('id')
            ->limit(20)
            ->get();

        return [
            'tgl' => $hariIni,
            'pesanan_hari_ini' => $pesananHariIni,
            'total_pesanan_hari_ini' => $pesananHariIni->sum(),
            'pesanan_besok' => $pesananBesok,
            'pesanan_mendatang' => $pesananMendatang,
            'prediksi_hari_ini' => Prediksi::whereDate('tgl_prediksi', $hariIni)->count(),
            'prediksi_besok' => Prediksi::whereDate('tgl_prediksi', $besok)->count(),
            'notifikasi_terkirim' => Pesanan::whereDate('tgl_ambil', $hariIni)->whereNotNull('notified_at')->exists(),
            'late_order_pending' => Pesanan::where('is_late_order', true)
                ->where('late_order_status', 'pending_approval')
                ->count(),
            'pembelian_hari_ini' => \App\Models\Pembelian::whereDate('tgl_beli', $hariIni)->exists(),
        ];
    }
}
