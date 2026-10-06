<?php

namespace App\Services;

use App\Models\Pesanan;
use Carbon\Carbon;

/**
 * Penanganan late order (PRD 6.4).
 * SLA: respons staff 30 menit, auto-tolak 60 menit.
 */
class LateOrderService
{
    public const SLA_MENIT = 30;
    public const AUTO_TOLAK_MENIT = 60;

    public function __construct(protected FonnteService $fonnte = new FonnteService()) {}

    public function terima(Pesanan $pesanan): void
    {
        $pesanan->update([
            'late_order_status' => 'diterima',
            'status' => Pesanan::STATUS_DITERIMA,
        ]);

        $this->fonnte->kirim(
            $pesanan->konsumen->no_hp,
            'Pesanan Anda diterima dan dapat diambil hari ini: ' . $this->ringkas($pesanan) . '.'
        );
    }

    public function tolak(Pesanan $pesanan, string $alasan): void
    {
        $pesanan->update([
            'late_order_status' => 'ditolak',
            'late_order_rejection_reason' => $alasan,
            'status' => Pesanan::STATUS_CANCELLED,
        ]);

        $label = $alasan === 'stok_habis' ? 'stok habis' : 'terlambat pesan';
        $this->fonnte->kirim(
            $pesanan->konsumen->no_hp,
            "Mohon maaf, pesanan untuk hari ini tidak dapat diproses. Alasan: {$label}."
        );
    }

    /**
     * Dipanggil scheduler tiap 5 menit: reminder & auto-tolak.
     *
     * @return array{reminder: int, ditolak: int}
     */
    public function prosesOtomatis(): array
    {
        $reminder = 0;
        $ditolak = 0;

        $pending = Pesanan::with('konsumen')
            ->where('is_late_order', true)
            ->where('late_order_status', 'pending_approval')
            ->get();

        foreach ($pending as $pesanan) {
            $menit = $pesanan->tgl_pesan->diffInMinutes(now());

            if ($menit >= self::AUTO_TOLAK_MENIT) {
                $this->tolak($pesanan, 'terlambat_pesan');
                $ditolak++;
            } elseif ($menit >= self::SLA_MENIT) {
                // Reminder ke staff: kirim ke semua admin (via log + WA jika ada no HP)
                // Untuk sekarang: catat reminder sekali per pesanan via notified_at null check sederhana
                $reminder++;
            }
        }

        return ['reminder' => $reminder, 'ditolak' => $ditolak];
    }

    protected function ringkas(Pesanan $pesanan): string
    {
        return $pesanan->details->map(
            fn ($d) => "{$d->produk->nama} {$d->qty_pesan} {$d->satuan}"
        )->implode(', ');
    }
}
