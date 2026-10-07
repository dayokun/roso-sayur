# Sistem Prediksi Adaptif Kebutuhan Stok Sayur & Buah — Roso Sayur

Aplikasi prediksi kebutuhan stok sayur & buah menggunakan **Fuzzy Tsukamoto**,
dengan pemesanan via **bot WhatsApp (Fonnte)** dan notifikasi batch agregat.

## Stack

- Laravel 13 / PHP 8.3 / MySQL 8
- Laravel Breeze (auth dashboard)
- Fonnte API (WhatsApp bot & notifikasi)
- maatwebsite/excel (export Excel), barryvdh/laravel-dompdf (export PDF)

## Instalasi

```bash
# 1. Install dependency
composer install
npm install && npm run build

# 2. Konfigurasi environment
cp .env.example .env
php artisan key:generate
```

Isi koneksi database di `.env`:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=roso_sayur
DB_USERNAME=roso
DB_PASSWORD=roso_secret_2026
```

Konfigurasi Fonnte di `.env`:

```
FONNTE_TOKEN=isi_token_fonnte
FONNTE_FAKE=true   # true = pesan hanya dicatat di storage/logs/fonnte.log
```

```bash
# 3. Migrasi + data dummy
php artisan migrate --seed

# 4. Jalankan
php artisan serve
```

Akun dashboard (dari seeder):

| Email               | Password   | Role            |
|---------------------|------------|-----------------|
| admin@rososayur.test | `password` | Admin IT        |
| roso@rososayur.test  | `password` | Admin Roso Sayur|

> Registrasi publik dimatikan — akun dashboard hanya dibuat oleh Admin IT
> lewat menu Pengguna. Konsumen tidak login (via bot WhatsApp).

## Scheduler

Daftarkan cron agar job otomatis jalan:

```bash
* * * * * cd /path/to/app && php artisan schedule:run >> /dev/null 2>&1
```

| Jadwal | Perintah | Keterangan |
|---|---|---|
| 20:00 WIB harian | `prediksi:jalankan` | Prediksi Fuzzy Tsukamoto |
| Tiap 5 menit | `late-order:proses` | Reminder & auto-tolak late order |
| 06:00 WIB harian | `evaluasi:hitung` | Hitung MAPE & MAE |

Perintah manual:

```bash
php artisan prediksi:jalankan --tanggal=2026-10-08
php artisan evaluasi:hitung --tanggal=2026-10-07
```

## Webhook WhatsApp

Arahkan webhook Fonnte ke:

```
POST https://domain-anda/webhook/fonnte
```

Alur bot: `pesan` → nama → pilih produk → qty → tambah lagi? → tanggal ambil →
konfirmasi `YA`. Sesi timeout 15 menit, maksimal 3x input salah.

## Target

- Waste rate < 10%
- MAPE < 20% (`qty_pesan` sebagai proxy demand)
- Fulfillment rate > 95%

## Testing

```bash
php artisan test
```
