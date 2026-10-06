<?php

namespace App\Http\Controllers;

use App\Models\Prediksi;
use App\Services\EvaluasiService;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Laporan evaluasi & agregat (PRD 6.10).
 */
class LaporanController extends Controller
{
    public function index(Request $request, EvaluasiService $evaluasi)
    {
        $sampai = Carbon::parse($request->get('sampai', Carbon::yesterday()->toDateString()));
        $dari = Carbon::parse($request->get('dari', $sampai->copy()->subDays(6)->toDateString()));

        $ringkasan = $evaluasi->ringkasan($dari, $sampai);

        $prediksis = Prediksi::with(['produk', 'details'])
            ->whereDate('tgl_prediksi', '>=', $dari->toDateString())
            ->whereDate('tgl_prediksi', '<=', $sampai->toDateString())
            ->orderBy('tgl_prediksi', 'desc')
            ->paginate(20);

        return view('laporan.index', [
            'dari' => $dari->toDateString(),
            'sampai' => $sampai->toDateString(),
            'ringkasan' => $ringkasan,
            'prediksis' => $prediksis,
        ]);
    }
}
