<?php

namespace App\Http\Controllers;

use App\Http\Requests\DisposisiSisaStokRequest;
use App\Models\SisaStok;
use App\Services\SisaStokService;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Sisa stok harian + disposisi (jual murah / buang).
 */
class SisaStokController extends Controller
{
    public function __construct(protected SisaStokService $service = new SisaStokService()) {}

    public function index(Request $request)
    {
        $tgl = Carbon::parse($request->get('tgl', Carbon::today()->toDateString()));

        return view('sisa-stok.index', [
            'tgl' => $tgl->toDateString(),
            'items' => $this->service->daftar($tgl),
        ]);
    }

    public function disposisiForm(SisaStok $sisaStok)
    {
        $sisaStok->load('pembelianDetail.produk');

        return view('sisa-stok.disposisi', ['sisa' => $sisaStok]);
    }

    public function disposisiStore(DisposisiSisaStokRequest $request, SisaStok $sisaStok)
    {
        $data = $request->validated();
        $this->service->disposisi(
            $sisaStok,
            $data['status_sisa'],
            $data['harga_jual_murah'] ?? null,
            $data['qty_terjual_murah'] ?? null,
            $data['qty_dibuang'] ?? null,
        );

        return redirect()->route('sisa-stok.index', ['tgl' => $sisaStok->tgl->toDateString()])
            ->with('sukses', 'Disposisi sisa stok tersimpan.');
    }
}
