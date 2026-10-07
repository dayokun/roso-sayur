<?php

use App\Http\Controllers\BotWebhookController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KonsumenController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\PesananController;
use App\Http\Controllers\PrediksiController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SisaStokController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing');
})->name('landing');

// Webhook WhatsApp (Fonnte)
Route::post('/webhook/fonnte', BotWebhookController::class)->name('webhook.fonnte');

Route::middleware(['auth', 'role:admin_it,admin_roso'])->group(function () {
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

    // Produk & kategori
    Route::get('/produk', [ProdukController::class, 'index'])->name('produk.index');
    Route::get('/produk/tambah', [ProdukController::class, 'create'])->name('produk.create');
    Route::post('/produk', [ProdukController::class, 'store'])->name('produk.store');
    Route::get('/produk/{produk}/edit', [ProdukController::class, 'edit'])->name('produk.edit');
    Route::put('/produk/{produk}', [ProdukController::class, 'update'])->name('produk.update');
    Route::delete('/produk/{produk}', [ProdukController::class, 'destroy'])->name('produk.destroy');
    Route::post('/kategori', [ProdukController::class, 'storeKategori'])->name('kategori.store');
    Route::put('/kategori/{kategori}', [ProdukController::class, 'updateKategori'])->name('kategori.update');

    // Konsumen
    Route::get('/konsumen', [KonsumenController::class, 'index'])->name('konsumen.index');

    // Prediksi
    Route::get('/prediksi', [PrediksiController::class, 'index'])->name('prediksi.index');

    // Sisa stok
    Route::get('/sisa-stok', [SisaStokController::class, 'index'])->name('sisa-stok.index');
    Route::get('/sisa-stok/{sisaStok}/disposisi', [SisaStokController::class, 'disposisiForm'])->name('sisa-stok.disposisi');
    Route::post('/sisa-stok/{sisaStok}/disposisi', [SisaStokController::class, 'disposisiStore'])->name('sisa-stok.disposisi.store');

    // Laporan
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/export-excel', [LaporanController::class, 'exportExcel'])->name('laporan.export-excel');
    Route::get('/laporan/export-pdf', [LaporanController::class, 'exportPdf'])->name('laporan.export-pdf');
});

// Khusus Admin IT
Route::middleware(['auth', 'role:admin_it'])->group(function () {
    Route::get('/notifikasi/unlock', [NotifikasiController::class, 'unlockForm'])->name('notifikasi.unlock');
    Route::post('/notifikasi/unlock', [NotifikasiController::class, 'unlockStore'])->name('notifikasi.unlock.store');
    Route::get('/notifikasi/log', [NotifikasiController::class, 'logIndex'])->name('notifikasi.log');

    Route::get('/pengguna', [UserController::class, 'index'])->name('users.index');
    Route::get('/pengguna/tambah', [UserController::class, 'create'])->name('users.create');
    Route::post('/pengguna', [UserController::class, 'store'])->name('users.store');
    Route::get('/pengguna/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/pengguna/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/pengguna/{user}', [UserController::class, 'destroy'])->name('users.destroy');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
