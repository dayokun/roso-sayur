<?php

namespace App\Services;

use App\Models\Pesanan;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

/**
 * Operasi pesanan untuk dashboard. Controller hanya meneruskan request.
 */
class PesananService
{
    public function __construct(
        protected FonnteService $fonnte = new FonnteService(),
        protected SisaStokService $sisaStok = new SisaStokService(),
        protected InvoiceService $invoice = new InvoiceService(),
    ) {}

    public function daftar(Carbon $tgl, ?string $status): Collection
    {
        $query = Pesanan::with(['konsumen', 'details.produk'])
            ->whereDate('tgl_ambil', $tgl->toDateString())
            ->latest('tgl_pesan');

        if ($status) {
            $query->where('status', $status);
        }

        return $query->get();
    }

    public function daftarLateOrder(): Collection
    {
        return Pesanan::with(['konsumen', 'details.produk'])
            ->where('is_late_order', true)
            ->where('late_order_status', 'pending_approval')
            ->latest('tgl_pesan')
            ->get();
    }

    public function terima(Pesanan $pesanan): void
    {
        if ($pesanan->status !== Pesanan::STATUS_PENDING) {
            throw new \RuntimeException('Hanya pesanan pending yang bisa diterima.');
        }

        $pesanan->update(['status' => Pesanan::STATUS_DITERIMA]);
        $kode = $pesanan->kode ?? "#{$pesanan->id}";
        $this->fonnte->kirim($pesanan->konsumen->no_hp, "Pesanan Anda (kode {$kode}) sedang kami proses.");
    }

    public function batalkan(Pesanan $pesanan, string $alasan): void
    {
        if ($pesanan->status === Pesanan::STATUS_SIAP_DIAMBIL) {
            throw new \RuntimeException('Pesanan yang sudah siap diambil tidak bisa dibatalkan.');
        }
        if ($pesanan->status === Pesanan::STATUS_SELESAI) {
            throw new \RuntimeException('Pesanan yang sudah selesai tidak bisa dibatalkan.');
        }

        $pesanan->update([
            'status' => Pesanan::STATUS_CANCELLED,
            'cancel_reason' => $alasan,
        ]);

        $kode = $pesanan->kode ?? "#{$pesanan->id}";
        $this->fonnte->kirim(
            $pesanan->konsumen->no_hp,
            "Pesanan Anda (kode {$kode}) telah dibatalkan.\nAlasan: {$alasan}\nMohon maaf atas ketidaknyamanannya."
        );
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

        $kode = $pesanan->kode ?? "#{$pesanan->id}";
        $this->fonnte->kirim(
            $pesanan->konsumen->no_hp,
            "Terima kasih. Pesanan (kode {$kode}) selesai. " . $pesanan->details->map(
                fn ($d) => "{$d->produk->nama} {$d->qty_delivered} {$d->satuan}"
            )->implode(', ')
        );

        // Kirim invoice PDF via WA
        try {
            $inv = $this->invoice->buatPdf($pesanan->fresh(['details.produk', 'konsumen']));
            $this->fonnte->kirimFile(
                $pesanan->konsumen->no_hp,
                $inv['url'],
                $inv['filename'],
                "Berikut kwitansi pembayaran pesanan {$kode} — total Rp " . number_format($inv['total'], 0, ',', '.') . ". Terima kasih."
            );
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::channel('fonnte')->error(
                "Gagal buat/kirim kwitansi {$kode}: {$e->getMessage()}"
            );
        }
    }
}
