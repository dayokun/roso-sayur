<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Pembelian — {{ $tgl }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('sukses'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">{{ session('sukses') }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-4 flex gap-3 items-end">
                <form method="GET" action="{{ route('pembelian.index') }}" class="flex gap-3 items-end">
                    <div>
                        <label class="text-sm text-gray-600">Tanggal beli</label>
                        <input type="date" name="tgl" value="{{ $tgl }}" class="block border rounded px-2 py-1" />
                    </div>
                    <button class="bg-gray-800 text-white px-4 py-2 rounded">Lihat</button>
                </form>
                <a href="{{ route('pembelian.create', ['tgl' => $tgl]) }}" class="ms-auto bg-green-700 text-white px-4 py-2 rounded">Input Actual</a>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <table class="datatable w-full text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="p-2 text-left">Produk</th>
                            <th class="p-2 text-left">Tersedia</th>
                            <th class="p-2 text-left">Qty Beli</th>
                            <th class="p-2 text-left">Harga Beli</th>
                            <th class="p-2 text-left">Sisa Stok</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pembelian?->details ?? [] as $d)
                            <tr class="border-t">
                                <td class="p-2">{{ $d->produk->nama }} ({{ $d->produk->satuan }})</td>
                                <td class="p-2">{{ $d->is_available_today ? 'Ya' : 'Tidak' }}</td>
                                <td class="p-2">{{ $d->qty_beli }}</td>
                                <td class="p-2">{{ $d->harga_beli ? 'Rp ' . number_format($d->harga_beli, 0, ',', '.') : '-' }}</td>
                                <td class="p-2">{{ $d->sisaStok->qty_sisa ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="p-4 text-center text-gray-500">Belum ada data pembelian tanggal ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
