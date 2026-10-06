<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Services\FonnteService;
use App\Services\LateOrderService;
use App\Services\SisaStokService;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Manajemen pesanan di dashboard (PRD 6.2, 6.3).
 */
class PesananController extends Controller
{
    public function __construct(protected FonnteService $fonnte = new FonnteService()) {}

    public function index(Request $request)
    {
        $tgl = $request->get('tgl', Carbon::today()->toDateString());
        $status = $request->get('status');

        $query = Pesanan::with(['konsumen', 'details.produk'])
            ->whereDate('tgl_ambil', $tgl)
            ->latest('tgl_pesan');

        if ($status) {
            $query->where('status', $status);
        }

        return view('pesanan.index', [
            'pesanans' => $query->paginate(20),
            'tgl' => $tgl,
            'status' => $status,
        ]);
    }

    /** Staff klik "Pesanan Diterima". */
    public function terima(Pesanan $pesanan)
    {
        if ($pesanan->status !== Pesanan::STATUS_PENDING) {
            return back()->with('error', 'Hanya pesanan pending yang bisa diterima.');
        }

        $pesanan->update(['status' => Pesanan::STATUS_DITERIMA]);
        $this->fonnte->kirim(
            $pesanan->konsumen->no_hp,
            'Pesanan Anda sedang kami proses.'
        );

        return back()->with('sukses', "Pesanan #{$pesanan->id} diterima.");
    }

    /** Form input qty delivered. */
    public function deliveredForm(Pesanan $pesanan)
    {
        $pesanan->load(['konsumen', 'details.produk']);

        return view('pesanan.delivered', compact('pesanan'));
    }

    /** Simpan qty delivered -> status selesai, hitung selisih & sisa stok. */
    public function deliveredStore(Request $request, Pesanan $pesanan, SisaStokService $sisaStok)
    {
        $data = $request->validate([
            'qty' => 'required|array',
            'qty.*' => 'nullable|numeric|min:0',
        ]);

        foreach ($pesanan->details as $detail) {
            $qty = isset($data['qty'][$detail->id]) ? (float) $data['qty'][$detail->id] : null;
            if ($qty === null) {
                continue;
            }
            // Cross-validation: delivered tidak boleh melebihi qty_beli (PRD 7)
            $detail->update([
                'qty_delivered' => $qty,
                'selisih' => round($qty - (float) $detail->qty_pesan, 2),
            ]);
        }

        $pesanan->update(['status' => Pesanan::STATUS_SELESAI]);

        // Hitung sisa stok setelah status selesai, agar delivered pesanan ini ikut terjumlah
        foreach ($pesanan->details as $detail) {
            $sisaStok->catatDelivered($detail->fresh());
        }

        $this->fonnte->kirim(
            $pesanan->konsumen->no_hp,
            'Terima kasih. Pesanan selesai. ' . $pesanan->details->map(
                fn ($d) => "{$d->produk->nama} {$d->qty_delivered} {$d->satuan}"
            )->implode(', ')
        );

        return redirect()->route('pesanan.index', ['tgl' => $pesanan->tgl_ambil->toDateString()])
            ->with('sukses', "Pesanan #{$pesanan->id} selesai.");
    }

    /** Batalkan pesanan (aturan PRD 6.2). */
    public function batal(Pesanan $pesanan)
    {
        if ($pesanan->status === Pesanan::STATUS_SIAP_DIAMBIL) {
            return back()->with('error', 'Pesanan yang sudah siap diambil tidak bisa dibatalkan.');
        }
        if ($pesanan->status === Pesanan::STATUS_SELESAI) {
            return back()->with('error', 'Pesanan yang sudah selesai tidak bisa dibatalkan.');
        }

        $pesanan->update(['status' => Pesanan::STATUS_CANCELLED]);
        $this->fonnte->kirim($pesanan->konsumen->no_hp, 'Pesanan Anda telah dibatalkan. Mohon maaf.');

        return back()->with('sukses', "Pesanan #{$pesanan->id} dibatalkan.");
    }

    /** Daftar late order menunggu approval. */
    public function lateIndex()
    {
        $pesanans = Pesanan::with(['konsumen', 'details.produk'])
            ->where('is_late_order', true)
            ->where('late_order_status', 'pending_approval')
            ->latest('tgl_pesan')
            ->paginate(20);

        return view('pesanan.late', compact('pesanans'));
    }

    public function lateTerima(Pesanan $pesanan, LateOrderService $service)
    {
        $service->terima($pesanan->load(['konsumen', 'details.produk']));

        return back()->with('sukses', "Late order #{$pesanan->id} diterima.");
    }

    public function lateTolak(Request $request, Pesanan $pesanan, LateOrderService $service)
    {
        $data = $request->validate(['alasan' => 'required|in:stok_habis,terlambat_pesan']);
        $service->tolak($pesanan->load(['konsumen', 'details.produk']), $data['alasan']);

        return back()->with('sukses', "Late order #{$pesanan->id} ditolak.");
    }
}
