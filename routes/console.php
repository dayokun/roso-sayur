<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// PRD 6.6.7: prediksi otomatis setiap hari pukul 20.00 WIB
Schedule::command('prediksi:jalankan')
    ->dailyAt('20:00')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping()
    ->onSuccess(fn () => logger()->info('Prediksi harian selesai'))
    ->onFailure(fn () => logger()->error('Prediksi harian GAGAL'));

// PRD 6.4: cek late order tiap 5 menit (reminder 30 mnt, auto-tolak 60 mnt)
Schedule::command('late-order:proses')
    ->everyFiveMinutes()
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping();

// PRD 6.10: evaluasi akurasi tiap pagi jam 06.00 untuk hari sebelumnya
Schedule::command('evaluasi:hitung')
    ->dailyAt('06:00')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping();
