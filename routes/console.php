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
