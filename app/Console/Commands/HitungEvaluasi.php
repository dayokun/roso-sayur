<?php

namespace App\Console\Commands;

use App\Services\EvaluasiService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class HitungEvaluasi extends Command
{
    protected $signature = 'evaluasi:hitung {--tanggal= : Tanggal evaluasi (Y-m-d), default kemarin}';

    protected $description = 'Hitung MAPE & MAE prediksi (PRD 6.10.3)';

    public function handle(EvaluasiService $service): int
    {
        $tgl = $this->option('tanggal') ? Carbon::parse($this->option('tanggal')) : Carbon::yesterday();
        $hasil = $service->hitung($tgl);
        $this->info("Evaluasi {$tgl->toDateString()}: {$hasil['dievaluasi']} prediksi dievaluasi.");

        return self::SUCCESS;
    }
}
