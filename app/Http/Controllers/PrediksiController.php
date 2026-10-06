<?php

namespace App\Http\Controllers;

use App\Services\PrediksiService;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Hasil prediksi harian + rincian aturan fuzzy aktif (transparansi).
 */
class PrediksiController extends Controller
{
    public function __construct(protected PrediksiService $service = new PrediksiService()) {}

    public function index(Request $request)
    {
        $tgl = Carbon::parse($request->get('tgl', Carbon::today()->toDateString()));

        return view('prediksi.index', [
            'tgl' => $tgl->toDateString(),
            'hasil' => $this->service->daftarHasil($tgl),
        ]);
    }
}
