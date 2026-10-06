<?php

use App\Http\Controllers\BotWebhookController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\PesananController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Webhook WhatsApp (Fonnte)
Route::post('/webhook/fonnte', BotWebhookController::class)->name('webhook.fonnte');

Route::middleware(['auth'])->group(function () {
    // Pesanan
    Route::get('/pesanan/late', [PesananController::class, 'lateIndex'])->name('pesanan.late');
    Route::post('/pesanan/{pesanan}/late-terima', [PesananController::class, 'lateTerima'])->name('pesanan.late-terima');
    Route::post('/pesanan/{pesanan}/late-tolak', [PesananController::class, 'lateTolak'])->name('pesanan.late-tolak');
    Route::get('/pesanan', [PesananController::class, 'index'])->name('pesanan.index');
    Route::post('/pesanan/{pesanan}/terima', [PesananController::class, 'terima'])->name('pesanan.terima');
    Route::get('/pesanan/{pesanan}/delivered', [PesananController::class, 'deliveredForm'])->name('pesanan.delivered');
    Route::post('/pesanan/{pesanan}/delivered', [PesananController::class, 'deliveredStore'])->name('pesanan.delivered.store');
    Route::post('/pesanan/{pesanan}/batal', [PesananController::class, 'batal'])->name('pesanan.batal');

    // Pembelian
    Route::get('/pembelian', [PembelianController::class, 'index'])->name('pembelian.index');
    Route::get('/pembelian/input', [PembelianController::class, 'create'])->name('pembelian.create');
    Route::post('/pembelian', [PembelianController::class, 'store'])->name('pembelian.store');

    // Notifikasi batch
    Route::get('/notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi.index');
    Route::post('/notifikasi/kirim', [NotifikasiController::class, 'kirim'])->name('notifikasi.kirim');
    Route::get('/notifikasi/unlock', [NotifikasiController::class, 'unlockForm'])->name('notifikasi.unlock');
    Route::post('/notifikasi/unlock', [NotifikasiController::class, 'unlockStore'])->name('notifikasi.unlock.store');

    // Laporan
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
