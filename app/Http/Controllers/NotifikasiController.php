<?php

namespace App\Http\Controllers;

use App\Http\Requests\UnlockNotifikasiRequest;
use App\Services\NotifikasiService;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Tombol "Proses & Kirim Notifikasi" + unlock (PRD 6.3, 6.3.1).
 * Thin controller: logika di NotifikasiService, otorisasi di Form Request.
 */
class NotifikasiController extends Controller
{
    public function __construct(protected NotifikasiService $service = new NotifikasiService()) {}

    public function index(Request $request)
    {
        $tgl = Carbon::parse($request->get('tgl', Carbon::today()->toDateString()));

        return view('notifikasi.index', [
            'tgl' => $tgl->toDateString(),
            'preview' => $this->service->preview($tgl),
        ]);
    }

    public function kirim(Request $request)
    {
        $tgl = Carbon::parse($request->get('tgl', Carbon::today()->toDateString()));

        try {
            $hasil = $this->service->kirim($tgl);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('sukses', "Notifikasi terkirim ke {$hasil['terkirim']} konsumen.");
    }

    public function unlockForm(Request $request)
    {
        $tgl = $request->get('tgl', Carbon::today()->toDateString());

        return view('notifikasi.unlock', compact('tgl'));
    }

    public function unlockStore(UnlockNotifikasiRequest $request)
    {
        $data = $request->validated();

        try {
            $this->service->unlock(Carbon::parse($data['tgl']), $request->user(), $data['alasan']);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }

        return redirect()->route('notifikasi.index', ['tgl' => $data['tgl']])
            ->with('sukses', 'Tombol notifikasi di-unlock. Audit tercatat.');
    }

    public function logIndex()
    {
        return view('notifikasi.log', ['logs' => $this->service->daftarLog()]);
    }
}
