<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use App\Models\Prediksi;
use App\Models\Produk;
use App\Services\PembelianService;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Input actual pembelian harian oleh staff (PRD 6.8).
 */
class PembelianController extends Controller
{
    public function index(Request $request)
    {
        $tgl = $request->get('tgl', Carbon::today()->toDateString());

        $pembelian = Pembelian::with(['details.produk'])
            ->whereDate('tgl_beli', $tgl)
            ->first();

        return view('pembelian.index', compact('pembelian', 'tgl'));
    }

    public function create(Request $request)
    {
        $tgl = $request->get('tgl', Carbon::today()->toDateString());

        $produks = Produk::with(['kategori'])
            ->where('is_available', true)
            ->orderBy('nama')
            ->get();

        // Referensi rekomendasi prediksi hari ini
        $rekomendasi = Prediksi::whereDate('tgl_prediksi', $tgl)->pluck('qty_dengan_buffer', 'produk_id');

        $sudah = Pembelian::whereDate('tgl_beli', $tgl)->first()?->details->keyBy('produk_id') ?? collect();

        return view('pembelian.create', compact('produks', 'tgl', 'rekomendasi', 'sudah'));
    }

    public function store(Request $request, PembelianService $service)
    {
        $data = $request->validate([
            'tgl' => 'required|date',
            'items' => 'required|array',
            'items.*.produk_id' => 'required|exists:produk,id',
            'items.*.qty_beli' => 'nullable|numeric|min:0',
            'items.*.harga_beli' => 'nullable|numeric|min:0',
            'items.*.is_available_today' => 'nullable|boolean',
            'catatan' => 'nullable|string',
        ]);

        $items = [];
        foreach ($data['items'] as $row) {
            $items[] = [
                'produk_id' => $row['produk_id'],
                'qty_beli' => $row['qty_beli'] ?? 0,
                'harga_beli' => $row['harga_beli'] ?? null,
                'is_available_today' => (bool) ($row['is_available_today'] ?? false),
            ];
        }

        $service->simpanActual(Carbon::parse($data['tgl']), $items, $data['catatan'] ?? null);

        return redirect()->route('pembelian.index', ['tgl' => $data['tgl']])
            ->with('sukses', 'Actual pembelian tersimpan.');
    }
}
