<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Roso Sayur — Sistem Prediksi Stok Sayur & Buah</title>
    <meta name="description" content="Sistem prediksi adaptif kebutuhan stok sayur & buah dengan Fuzzy Tsukamoto dan pemesanan via WhatsApp.">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-gray-800 antialiased bg-white">

    <!-- Navbar -->
    <nav class="fixed top-0 inset-x-0 z-50 bg-white/90 backdrop-blur border-b border-green-100">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
            <a href="/" class="flex items-center gap-2">
                <span class="w-9 h-9 rounded-xl bg-green-600 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 3c3.5 0 8.5 1.5 11 6.5C18.5 14.5 16 20 12 21c.5-4 .5-8-1.5-11C8 7 5 5 5 3z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21c-4 0-8-3-9-8"/>
                    </svg>
                </span>
                <span class="font-extrabold text-xl text-green-800 tracking-tight">Roso Sayur</span>
            </a>
            <div class="hidden md:flex items-center gap-8 text-sm font-medium text-gray-600">
                <a href="#fitur" class="hover:text-green-700">Fitur</a>
                <a href="#cara-pesan" class="hover:text-green-700">Cara Pesan</a>
                <a href="#tentang" class="hover:text-green-700">Tentang</a>
            </div>
            <a href="{{ route('login') }}" class="px-5 py-2.5 rounded-full bg-green-600 text-white text-sm font-semibold hover:bg-green-700 transition shadow-sm">
                Masuk Dashboard
            </a>
        </div>
    </nav>

    <!-- Hero -->
    <header class="pt-32 pb-20 bg-gradient-to-b from-green-50 via-white to-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 text-center">
            <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-green-100 text-green-800 text-xs font-semibold tracking-wide uppercase">
                <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                Sistem Prediksi Adaptif
            </span>
            <h1 class="mt-6 text-4xl sm:text-5xl lg:text-6xl font-extrabold text-gray-900 tracking-tight leading-tight">
                Stok Sayur &amp; Buah Tepat,<br>
                <span class="text-green-600">Setiap Hari.</span>
            </h1>
            <p class="mt-6 max-w-2xl mx-auto text-lg text-gray-600">
                Roso Sayur memprediksi kebutuhan stok harian dengan <strong class="text-gray-800">Fuzzy Tsukamoto</strong>,
                menerima pesanan lewat <strong class="text-gray-800">bot WhatsApp</strong>, dan menekan angka sisa buang —
                segar untuk pelanggan, hemat untuk usaha.
            </p>
            <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('login') }}" class="w-full sm:w-auto px-8 py-3.5 rounded-full bg-green-600 text-white font-semibold hover:bg-green-700 transition shadow-lg shadow-green-600/20">
                    Masuk Dashboard
                </a>
                <a href="#cara-pesan" class="w-full sm:w-auto px-8 py-3.5 rounded-full border-2 border-green-600 text-green-700 font-semibold hover:bg-green-50 transition">
                    Cara Pesan via WhatsApp
                </a>
            </div>

            <!-- Stats -->
            <div class="mt-16 grid grid-cols-2 md:grid-cols-4 gap-4 max-w-3xl mx-auto">
                <div class="rounded-2xl bg-white border border-green-100 p-5 shadow-sm">
                    <p class="text-3xl font-extrabold text-green-600">&lt;10%</p>
                    <p class="mt-1 text-sm text-gray-500">Target waste rate</p>
                </div>
                <div class="rounded-2xl bg-white border border-green-100 p-5 shadow-sm">
                    <p class="text-3xl font-extrabold text-green-600">&lt;20%</p>
                    <p class="mt-1 text-sm text-gray-500">Target MAPE prediksi</p>
                </div>
                <div class="rounded-2xl bg-white border border-green-100 p-5 shadow-sm">
                    <p class="text-3xl font-extrabold text-green-600">&gt;95%</p>
                    <p class="mt-1 text-sm text-gray-500">Target fulfillment</p>
                </div>
                <div class="rounded-2xl bg-white border border-green-100 p-5 shadow-sm">
                    <p class="text-3xl font-extrabold text-green-600">27</p>
                    <p class="mt-1 text-sm text-gray-500">Aturan fuzzy aktif</p>
                </div>
            </div>
        </div>
    </header>

    <!-- Fitur -->
    <section id="fitur" class="py-20 bg-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6">
            <div class="text-center max-w-2xl mx-auto">
                <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">Satu sistem, semua kebutuhan operasional</h2>
                <p class="mt-4 text-gray-600">Dari prediksi stok sampai laporan — dirancang untuk ritme harian toko sayur.</p>
            </div>
            <div class="mt-12 grid md:grid-cols-3 gap-6">
                <div class="rounded-2xl border border-gray-100 bg-green-50/50 p-7 hover:shadow-lg hover:border-green-200 transition">
                    <div class="w-12 h-12 rounded-xl bg-green-600 text-white flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <h3 class="mt-5 font-bold text-lg text-gray-900">Prediksi Fuzzy Tsukamoto</h3>
                    <p class="mt-2 text-sm text-gray-600 leading-relaxed">Kebutuhan stok besok dihitung dari tren penjualan, hari dalam minggu, dan anomali — transparan sampai ke tiap aturan fuzzy yang aktif.</p>
                </div>
                <div class="rounded-2xl border border-gray-100 bg-green-50/50 p-7 hover:shadow-lg hover:border-green-200 transition">
                    <div class="w-12 h-12 rounded-xl bg-green-600 text-white flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    </div>
                    <h3 class="mt-5 font-bold text-lg text-gray-900">Bot WhatsApp</h3>
                    <p class="mt-2 text-sm text-gray-600 leading-relaxed">Pelanggan pesan langsung via WhatsApp tanpa install aplikasi — bot memandu dari katalog sampai konfirmasi pesanan.</p>
                </div>
                <div class="rounded-2xl border border-gray-100 bg-green-50/50 p-7 hover:shadow-lg hover:border-green-200 transition">
                    <div class="w-12 h-12 rounded-xl bg-green-600 text-white flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    </div>
                    <h3 class="mt-5 font-bold text-lg text-gray-900">Notifikasi Stok Batch</h3>
                    <p class="mt-2 text-sm text-gray-600 leading-relaxed">Info ketersediaan dikirim otomatis ke pelanggan berdasarkan hasil prediksi — dengan riwayat buka-kunci yang teraudit.</p>
                </div>
                <div class="rounded-2xl border border-gray-100 bg-green-50/50 p-7 hover:shadow-lg hover:border-green-200 transition">
                    <div class="w-12 h-12 rounded-xl bg-green-600 text-white flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <h3 class="mt-5 font-bold text-lg text-gray-900">Laporan Excel &amp; PDF</h3>
                    <p class="mt-2 text-sm text-gray-600 leading-relaxed">Unduh laporan akurasi prediksi, fulfillment, dan waste rate dalam format Excel atau PDF siap cetak.</p>
                </div>
                <div class="rounded-2xl border border-gray-100 bg-green-50/50 p-7 hover:shadow-lg hover:border-green-200 transition">
                    <div class="w-12 h-12 rounded-xl bg-green-600 text-white flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </div>
                    <h3 class="mt-5 font-bold text-lg text-gray-900">Kelola Sisa Stok</h3>
                    <p class="mt-2 text-sm text-gray-600 leading-relaxed">Sisa stok harian tercatat disposisinya — dijual obral, dibuang, atau disimpan — untuk menekan waste rate.</p>
                </div>
                <div class="rounded-2xl border border-gray-100 bg-green-50/50 p-7 hover:shadow-lg hover:border-green-200 transition">
                    <div class="w-12 h-12 rounded-xl bg-green-600 text-white flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="mt-5 font-bold text-lg text-gray-900">Evaluasi Akurasi</h3>
                    <p class="mt-2 text-sm text-gray-600 leading-relaxed">MAPE &amp; MAE dihitung otomatis dari data aktual — performa prediksi selalu terpantau.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Cara pesan -->
    <section id="cara-pesan" class="py-20 bg-green-900 text-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6">
            <div class="text-center max-w-2xl mx-auto">
                <h2 class="text-3xl font-extrabold tracking-tight">Pesan sayur semudah chat</h2>
                <p class="mt-4 text-green-100">Nggak perlu install aplikasi. Cukup chat bot WhatsApp Roso Sayur:</p>
            </div>
            <div class="mt-12 grid md:grid-cols-3 gap-6">
                <div class="rounded-2xl bg-green-800/60 border border-green-700 p-7">
                    <p class="text-4xl font-extrabold text-green-300">1</p>
                    <h3 class="mt-3 font-bold text-lg">Sapa bot</h3>
                    <p class="mt-2 text-sm text-green-100 leading-relaxed">Kirim pesan apa saja ke nomor WhatsApp Roso Sayur untuk mulai sesi pemesanan.</p>
                </div>
                <div class="rounded-2xl bg-green-800/60 border border-green-700 p-7">
                    <p class="text-4xl font-extrabold text-green-300">2</p>
                    <h3 class="mt-3 font-bold text-lg">Pilih produk</h3>
                    <p class="mt-2 text-sm text-green-100 leading-relaxed">Bot menampilkan katalog — pilih sayur &amp; buah beserta jumlah yang diinginkan, bisa banyak item sekaligus.</p>
                </div>
                <div class="rounded-2xl bg-green-800/60 border border-green-700 p-7">
                    <p class="text-4xl font-extrabold text-green-300">3</p>
                    <h3 class="mt-3 font-bold text-lg">Konfirmasi</h3>
                    <p class="mt-2 text-sm text-green-100 leading-relaxed">Cek ringkasan pesanan, konfirmasi, dan pesananmu tercatat untuk pengiriman besok.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Tentang -->
    <section id="tentang" class="py-20 bg-white">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 text-center">
            <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">Tentang Roso Sayur</h2>
            <p class="mt-6 text-gray-600 leading-relaxed">
                Roso Sayur adalah sistem prediksi adaptif kebutuhan stok sayur dan buah. Setiap malam pukul 20:00,
                sistem menghitung kebutuhan stok esok hari menggunakan metode <strong class="text-gray-800">Fuzzy Tsukamoto</strong>
                dengan 27 aturan berbasis tren penjualan, pola harian, dan deteksi anomali — lalu menambahkan
                <em>safety buffer</em> per kategori produk agar rak selalu terisi tanpa berlebihan.
            </p>
            <p class="mt-4 text-gray-600 leading-relaxed">
                Hasilnya: pelanggan selalu dapat produk segar, dan sisa buang terus ditekan di bawah 10%.
            </p>
        </div>
    </section>

    <!-- CTA -->
    <section class="py-16 bg-gradient-to-r from-green-600 to-green-700">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 text-center text-white">
            <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Kelola operasional tokomu hari ini</h2>
            <p class="mt-3 text-green-100">Masuk ke dashboard untuk pesanan, prediksi, dan laporan.</p>
            <a href="{{ route('login') }}" class="mt-8 inline-block px-10 py-3.5 rounded-full bg-white text-green-700 font-bold hover:bg-green-50 transition shadow-lg">
                Masuk Dashboard
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-10 bg-gray-900 text-gray-400">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <span class="w-8 h-8 rounded-lg bg-green-600 flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 3c3.5 0 8.5 1.5 11 6.5C18.5 14.5 16 20 12 21c.5-4 .5-8-1.5-11C8 7 5 5 5 3z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21c-4 0-8-3-9-8"/>
                    </svg>
                </span>
                <span class="font-bold text-white">Roso Sayur</span>
            </div>
            <p class="text-sm">Sistem Prediksi Adaptif Kebutuhan Stok Sayur &amp; Buah &copy; {{ date('Y') }}</p>
        </div>
    </footer>

</body>
</html>
