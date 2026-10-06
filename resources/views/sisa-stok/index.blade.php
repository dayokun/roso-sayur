<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Sisa Stok — {{ $tgl }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('sukses'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">{{ session('sukses') }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-4">
                <form method="GET" action="{{ route('sisa-stok.index') }}" class="flex gap-3 items-end">
                    <div>
                        <label class="text-sm text-gray-600">Tanggal</label>
                        <input type="date" name="tgl" value="{{ $tgl }}" class="block border rounded px-2 py-1" />
                    </div>
                    <button class="bg-gray-800 text-white px-4 py-2 rounded">Lihat</button>
                </form>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="p-2 text-left">Produk</th>
                            <th class="p-2 text-left">Qty Sisa</th>
                            <th class="p-2 text-left">Disposisi</th>
                            <th class="p-2 text-left">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($items as $s)
                            <tr class="border-t">
                                <td class="p-2">{{ $s->pembelianDetail->produk->nama }} ({{ $s->pembelianDetail->produk->satuan }})</td>
                                <td class="p-2">{{ number_format($s->qty_sisa, 2) }}</td>
                                <td class="p-2">
                                    @if ($s->status_sisa === 'dijual_murah')
                                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">Jual murah @ Rp {{ number_format($s->harga_jual_murah, 0, ',', '.') }}</span>
                                    @elseif ($s->status_sisa === 'dibuang')
                                        <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs">Dibuang</span>
                                    @else
                                        <span class="text-gray-500">-</span>
                                    @endif
                                </td>
                                <td class="p-2">
                                    <a href="{{ route('sisa-stok.disposisi', $s->id) }}" class="text-blue-600 underline">Disposisi</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="p-4 text-center text-gray-500">Belum ada sisa stok tanggal ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
