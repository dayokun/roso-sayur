<?php

namespace App\Console\Commands;

use App\Services\PrediksiService;
use Carbon\Carbon;
use Illuminate\Console\Command;

/**
 * Menjalankan prediksi stok harian (PRD 6.6.7).
 * Dijadwalkan tiap pukul 20.00 WIB via scheduler.
 */
class JalankanPrediksi extends Command
{
    protected $signature = 'prediksi:jalankan {--tanggal= : Tanggal target prediksi (Y-m-d), default besok}';

    protected $description = 'Jalankan prediksi kebutuhan stok Fuzzy Tsukamoto untuk semua produk aktif';

    public function handle(PrediksiService $service): int
    {
        $tglTarget = $this->option('tanggal')
            ? Carbon::parse($this->option('tanggal'))
            : Carbon::tomorrow();

        $this->info("Prediksi untuk tanggal ambil: {$tglTarget->toDateString()}");
        $this->newLine();

        $hasil = $service->jalankan($tglTarget);

        $rows = [];
        foreach ($hasil['produk'] as $r) {
            $flag = ($r['butuh_perhatian'] ?? false) ? ' [PERHATIAN]' : '';
            $flag .= ($r['outlier_dicap'] ?? false) ? ' [outlier dicap]' : '';
            $rows[] = [
                $r['produk'],
                $r['metode'],
                $r['qty_rekomendasi'],
                $r['qty_dengan_buffer'],
                ($r['rules_aktif'] ?? '-') . $flag,
            ];
        }

        if ($rows) {
            $this->table(['Produk', 'Metode', 'Rekomendasi', '+Buffer', 'Rules'], $rows);
        }

        foreach ($hasil['dilewati'] as $s) {
            $this->warn("DILEWATI: {$s['produk']} — {$s['alasan']}");
        }

        $this->newLine();
        $this->info(count($hasil['produk']) . ' produk diprediksi, ' . count($hasil['dilewati']) . ' dilewati.');

        return self::SUCCESS;
    }
}
