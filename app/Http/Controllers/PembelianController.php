<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePembelianRequest;
use App\Services\PembelianService;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Input actual pembelian harian oleh staff (PRD 6.8).
 * Thin controller: query & logika di PembelianService.
 */
class PembelianController extends Controller
{
    public function __construct(protected PembelianService $service = new PembelianService()) {}

    public function index(Request $request)
    {
        $tgl = Carbon::parse($request->get('tgl', Carbon::today()->toDateString()));

        return view('pembelian.index', [
            'pembelian' => $this->service->daftar($tgl),
            'tgl' => $tgl->toDateString(),
        ]);
    }

    public function create(Request $request)
    {
        $tgl = Carbon::parse($request->get('tgl', Carbon::today()->toDateString()));

        return view('pembelian.create', array_merge(
            ['tgl' => $tgl->toDateString()],
            $this->service->formData($tgl),
        ));
    }

    public function store(StorePembelianRequest $request)
    {
        $data = $request->validated();

        $items = [];
        foreach ($data['items'] as $row) {
            $items[] = [
                'produk_id' => $row['produk_id'],
                'qty_beli' => $row['qty_beli'] ?? 0,
                'harga_beli' => $row['harga_beli'] ?? null,
                'is_available_today' => (bool) ($row['is_available_today'] ?? false),
            ];
        }

        $this->service->simpanActual(Carbon::parse($data['tgl']), $items, $data['catatan'] ?? null);

        return redirect()->route('pembelian.index', ['tgl' => $data['tgl']])
            ->with('sukses', 'Actual pembelian tersimpan.');
    }
}
