<?php

namespace App\Http\Controllers;

use App\Exports\LaporanExport;
use App\Models\Prediksi;
use App\Services\EvaluasiService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Laporan evaluasi & agregat (PRD 6.10).
 */
class LaporanController extends Controller
{
    public function index(Request $request, EvaluasiService $evaluasi)
    {
        [$dari, $sampai] = $this->periode($request);

        $ringkasan = $evaluasi->ringkasan($dari, $sampai);

        $prediksis = Prediksi::with(['produk', 'details'])
            ->whereDate('tgl_prediksi', '>=', $dari->toDateString())
            ->whereDate('tgl_prediksi', '<=', $sampai->toDateString())
            ->orderBy('tgl_prediksi', 'desc')
            ->get();

        return view('laporan.index', [
            'dari' => $dari->toDateString(),
            'sampai' => $sampai->toDateString(),
            'ringkasan' => $ringkasan,
            'prediksis' => $prediksis,
        ]);
    }

    public function exportExcel(Request $request, EvaluasiService $evaluasi)
    {
        [$dari, $sampai] = $this->periode($request);

        return Excel::download(
            new LaporanExport($evaluasi->ringkasan($dari, $sampai), $dari, $sampai),
            "laporan-{$dari->toDateString()}_{$sampai->toDateString()}.xlsx"
        );
    }

    public function exportPdf(Request $request, EvaluasiService $evaluasi)
    {
        [$dari, $sampai] = $this->periode($request);

        $pdf = Pdf::loadView('laporan.pdf', [
            'dari' => $dari->toDateString(),
            'sampai' => $sampai->toDateString(),
            'ringkasan' => $evaluasi->ringkasan($dari, $sampai),
        ]);

        return $pdf->download("laporan-{$dari->toDateString()}_{$sampai->toDateString()}.pdf");
    }

    protected function periode(Request $request): array
    {
        $sampai = Carbon::parse($request->get('sampai', Carbon::yesterday()->toDateString()));
        $dari = Carbon::parse($request->get('dari', $sampai->copy()->subDays(6)->toDateString()));

        return [$dari, $sampai];
    }
}
