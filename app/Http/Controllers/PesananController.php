<?php

namespace App\Http\Controllers;

use App\Http\Requests\BatalkanPesananRequest;
use App\Http\Requests\LateTolakRequest;
use App\Http\Requests\UpdateDeliveredRequest;
use App\Models\Pesanan;
use App\Services\LateOrderService;
use App\Services\PesananService;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Manajemen pesanan di dashboard (PRD 6.2, 6.3).
 * Thin controller: semua query & logika bisnis di PesananService.
 */
class PesananController extends Controller
{
    public function __construct(protected PesananService $service = new PesananService()) {}

    public function index(Request $request)
    {
        $tgl = Carbon::parse($request->get('tgl', Carbon::today()->toDateString()));
        $status = $request->get('status');

        return view('pesanan.index', [
            'pesanans' => $this->service->daftar($tgl, $status),
            'tgl' => $tgl->toDateString(),
            'status' => $status,
        ]);
    }

    public function terima(Pesanan $pesanan)
    {
        try {
            $this->service->terima($pesanan);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('sukses', "Pesanan " . ($pesanan->kode ?? "#{$pesanan->id}") . " diterima.");
    }

    public function deliveredForm(Pesanan $pesanan)
    {
        $pesanan->load(['konsumen', 'details.produk']);

        return view('pesanan.delivered', compact('pesanan'));
    }

    public function deliveredStore(UpdateDeliveredRequest $request, Pesanan $pesanan)
    {
        $this->service->simpanDelivered($pesanan, $request->validated()['qty'] ?? []);

        return redirect()->route('pesanan.index', ['tgl' => $pesanan->tgl_ambil->toDateString()])
            ->with('sukses', "Pesanan " . ($pesanan->kode ?? "#{$pesanan->id}") . " selesai.");
    }

    public function batal(BatalkanPesananRequest $request, Pesanan $pesanan)
    {
        try {
            $this->service->batalkan($pesanan, $request->validated()['alasan']);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('sukses', "Pesanan " . ($pesanan->kode ?? "#{$pesanan->id}") . " dibatalkan.");
    }

    public function lateIndex()
    {
        return view('pesanan.late', ['pesanans' => $this->service->daftarLateOrder()]);
    }

    public function lateTerima(Pesanan $pesanan, LateOrderService $service)
    {
        $service->terima($pesanan->load(['konsumen', 'details.produk']));

        return back()->with('sukses', "Late order " . ($pesanan->kode ?? "#{$pesanan->id}") . " diterima.");
    }

    public function lateTolak(LateTolakRequest $request, Pesanan $pesanan, LateOrderService $service)
    {
        $service->tolak($pesanan->load(['konsumen', 'details.produk']), $request->validated()['alasan']);

        return back()->with('sukses', "Late order " . ($pesanan->kode ?? "#{$pesanan->id}") . " ditolak.");
    }
}
