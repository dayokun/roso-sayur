<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }} — {{ $ringkasan['tgl'] }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white shadow-sm sm:rounded-lg p-4">
                    <div class="text-sm text-gray-500">Pesanan hari ini</div>
                    <div class="text-3xl font-bold">{{ $ringkasan['total_pesanan_hari_ini'] }}</div>
                    <div class="text-xs text-gray-500 mt-1">
                        @foreach ($ringkasan['pesanan_hari_ini'] as $status => $jml)
                            {{ $status }}: {{ $jml }}@if(!$loop->last), @endif
                        @endforeach
                    </div>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-4">
                    <div class="text-sm text-gray-500">Pesanan besok</div>
                    <div class="text-3xl font-bold">{{ $ringkasan['pesanan_besok'] }}</div>
                    <div class="text-xs text-gray-500 mt-1">menunggu diproses</div>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-4">
                    <div class="text-sm text-gray-500">Prediksi</div>
                    <div class="text-3xl font-bold">{{ $ringkasan['prediksi_besok'] }}</div>
                    <div class="text-xs text-gray-500 mt-1">
                        produk diprediksi untuk besok
                        @if ($ringkasan['prediksi_besok'] === 0)
                            <span class="text-amber-600 font-semibold">(jalan 20:00 WIB)</span>
                        @endif
                    </div>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-4">
                    <div class="text-sm text-gray-500">Late order pending</div>
                    <div class="text-3xl font-bold {{ $ringkasan['late_order_pending'] > 0 ? 'text-amber-600' : '' }}">{{ $ringkasan['late_order_pending'] }}</div>
                    <div class="text-xs text-gray-500 mt-1">
                        @if ($ringkasan['late_order_pending'] > 0)
                            <a href="{{ route('pesanan.late') }}" class="text-blue-600 underline">Proses sekarang</a>
                        @else
                            tidak ada
                        @endif
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white shadow-sm sm:rounded-lg p-4">
                    <div class="text-sm text-gray-500 mb-2">Notifikasi hari ini</div>
                    @if ($ringkasan['notifikasi_terkirim'])
                        <span class="bg-green-100 text-green-800 px-3 py-1 rounded text-sm font-semibold">Sudah dikirim</span>
                    @else
                        <span class="bg-amber-100 text-amber-800 px-3 py-1 rounded text-sm font-semibold">Belum dikirim</span>
                        <a href="{{ route('notifikasi.index') }}" class="ms-2 text-blue-600 underline text-sm">Kirim sekarang</a>
                    @endif
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-4">
                    <div class="text-sm text-gray-500 mb-2">Actual pembelian hari ini</div>
                    @if ($ringkasan['pembelian_hari_ini'])
                        <span class="bg-green-100 text-green-800 px-3 py-1 rounded text-sm font-semibold">Sudah diinput</span>
                    @else
                        <span class="bg-amber-100 text-amber-800 px-3 py-1 rounded text-sm font-semibold">Belum diinput</span>
                        <a href="{{ route('pembelian.create') }}" class="ms-2 text-blue-600 underline text-sm">Input sekarang</a>
                    @endif
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-4">
                <div class="text-sm text-gray-500 mb-2">Pesanan mendatang <span class="text-xs">(tanggal ambil setelah hari ini)</span></div>
                @if ($ringkasan['pesanan_mendatang']->isEmpty())
                    <p class="text-sm text-gray-400">Tidak ada pesanan mendatang.</p>
                @else
                    <ul class="divide-y divide-gray-100 text-sm">
                        @foreach ($ringkasan['pesanan_mendatang'] as $p)
                            <li class="py-2 flex items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <span class="font-mono font-semibold">{{ $p->kode }}</span>
                                    <span class="text-gray-600">{{ $p->konsumen->nama ?? '-' }}</span>
                                    <span class="text-gray-400">· {{ $p->tgl_ambil->format('d M Y') }} · {{ $p->status }}</span>
                                </div>
                                <a href="{{ route('pesanan.index', ['tgl' => $p->tgl_ambil->toDateString()]) }}" class="text-blue-600 underline shrink-0">Lihat</a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
