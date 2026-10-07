<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Notifikasi — {{ $tgl }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('sukses'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">{{ session('sukses') }}</div>
            @endif
            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">{{ session('error') }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-4">
                <form method="GET" action="{{ route('notifikasi.index') }}" class="flex gap-3 items-end">
                    <div>
                        <label class="text-sm text-gray-600">Tanggal</label>
                        <input type="date" name="tgl" value="{{ $tgl }}" class="block border rounded px-2 py-1" />
                    </div>
                    <button class="bg-gray-800 text-white px-4 py-2 rounded">Preview</button>
                </form>
            </div>

            @if ($preview['terkunci'])
                <div class="bg-amber-100 border border-amber-400 text-amber-800 px-4 py-3 rounded">
                    Tombol terkunci — notifikasi sudah dikirim untuk tanggal ini.
                    @if(auth()->user()->isAdminIt())
                        <a href="{{ route('notifikasi.unlock', ['tgl' => $tgl]) }}" class="underline font-semibold">Minta unlock (Admin IT)</a>
                    @else
                        Hubungi Admin IT untuk unlock.
                    @endif
                </div>
            @endif

            @if(auth()->user()->isAdminIt())
                <div class="text-sm">
                    <a href="{{ route('notifikasi.log') }}" class="text-blue-600 underline">Lihat riwayat unlock (audit trail)</a>
                </div>
            @endif

            @if (! empty($preview['belum_diinput']))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    Pre-check gagal — produk berikut belum diinput actual beli:
                    <strong>{{ implode(', ', $preview['belum_diinput']) }}</strong>.
                    <a href="{{ route('pembelian.create', ['tgl' => $tgl]) }}" class="underline">Input sekarang</a>
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <table class="datatable w-full text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="p-2 text-left">Produk</th>
                            <th class="p-2 text-left">Qty Beli</th>
                            <th class="p-2 text-left">Total Pesan</th>
                            <th class="p-2 text-left">Skenario</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($preview['produk'] as $ev)
                            <tr class="border-t">
                                <td class="p-2">{{ $ev['nama'] }}</td>
                                <td class="p-2">{{ number_format($ev['qty_beli'], 2) }}</td>
                                <td class="p-2">{{ number_format($ev['total_pesan'], 2) }}</td>
                                <td class="p-2">
                                    @if ($ev['skenario'] === 'aman')
                                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded">Stok Aman</span>
                                    @elseif ($ev['skenario'] === 'terbatas')
                                        <span class="bg-amber-100 text-amber-800 px-2 py-1 rounded">Stok Terbatas</span>
                                    @else
                                        <span class="bg-red-100 text-red-800 px-2 py-1 rounded">Tidak Tersedia</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="p-4 text-center text-gray-500">Belum ada pesanan untuk tanggal ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if (! $preview['terkunci'] && ! empty($preview['produk']))
                <form method="POST" action="{{ route('notifikasi.kirim') }}" onsubmit="return confirm('Kirim notifikasi batch ke semua konsumen?')">
                    @csrf
                    <input type="hidden" name="tgl" value="{{ $tgl }}" />
                    <button class="bg-blue-700 text-white px-6 py-3 rounded font-semibold">Proses & Kirim Notifikasi</button>
                </form>
            @endif
        </div>
    </div>
</x-app-layout>
