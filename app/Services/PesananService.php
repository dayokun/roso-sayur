<?php

namespace App\Services;

use App\Models\Pesanan;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Operasi pesanan untuk dashboard. Controller hanya meneruskan request.
 */
class PesananService
{
    public function __construct(
        protected FonnteService $fonnte = new FonnteService(),
        protected SisaStokService $sisaStok = new SisaStokService(),
    ) {}

    public function daftar(Carbon $tgl, ?string $status): LengthAwarePaginator
    {
        $query = Pesanan::with(['konsumen', 'details.produk'])
            ->whereDate('tgl_ambil', $tgl->toDateString())
            ->latest('tgl_pesan');

        if ($status) {
            $query->where('status', $status);
        }

        return $query->paginate(20);
    }

    public function daftarLateOrder(): LengthAwarePaginator
    {
        return Pesanan::with(['konsumen', 'details.produk'])
            ->where('is_late_order', true)
            ->where('late_order_status', 'pending_approval')
            ->latest('tgl_pesan')
            ->paginate(20);
    }

    public function terima(Pesanan $pesanan): void
    {
        if ($pesanan->status !== Pesanan::STATUS_PENDING) {
            throw new \RuntimeException('Hanya pesanan pending yang bisa diterima.');
        }

        $pesanan->update(['status' => Pesanan::STATUS_DITERIMA]);
        $this->fonnte->kirim($pesanan->konsumen->no_hp, 'Pesanan Anda sedang kami proses.');
    }

    public function batalkan(Pesanan $pesanan): void
    {
        if ($pesanan->status === Pesanan::STATUS_SIAP_DIAMBIL) {
            throw new \RuntimeException('Pesanan yang sudah siap diambil tidak bisa dibatalkan.');
        }
        if ($pesanan->status === Pesanan::STATUS_SELESAI) {
            throw new \RuntimeException('Pesanan yang sudah selesai tidak bisa dibatalkan.');
        }

        $pesanan->update(['status' => Pesanan::STATUS_CANCELLED]);
        $this->fonnte->kirim($pesanan->konsumen->no_hp, 'Pesanan Anda telah dibatalkan. Mohon maaf.');
    }

    /**
     * Simpan qty delivered -> status selesai, hitung selisih & sisa stok.
     *
     * @param  array<int, float|null>  $qtyMap  [pesanan_detail_id => qty]
     */
    public function simpanDelivered(Pesanan $pesanan, array $qtyMap): void
    {
        foreach ($pesanan->details as $detail) {
            $qty = $qtyMap[$detail->id] ?? null;
            if ($qty === null) {
                continue;
            }
            $detail->update([
                'qty_delivered' => (float) $qty,
                'selisih' => round((float) $qty - (float) $detail->qty_pesan, 2),
            ]);
        }

        $pesanan->update(['status' => Pesanan::STATUS_SELESAI]);

        foreach ($pesanan->details as $detail) {
            $this->sisaStok->catatDelivered($detail->fresh());
        }

        $this->fonnte->kirim(
            $pesanan->konsumen->no_hp,
            'Terima kasih. Pesanan selesai. ' . $pesanan->details->map(
                fn ($d) => "{$d->produk->nama} {$d->qty_delivered} {$d->satuan}"
            )->implode(', ')
        );
    }
}
