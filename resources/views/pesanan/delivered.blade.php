<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Input Delivered — Pesanan #{{ $pesanan->id }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <p class="mb-4 text-gray-700">Konsumen: <strong>{{ $pesanan->konsumen->nama }}</strong> ({{ $pesanan->konsumen->no_hp }})</p>
                <form method="POST" action="{{ route('pesanan.delivered.store', $pesanan) }}">
                    @csrf
                    <table class="w-full text-sm mb-4">
                        <thead class="bg-gray-100">
                            <tr><th class="p-2 text-left">Produk</th><th class="p-2 text-left">Qty Pesan</th><th class="p-2 text-left">Qty Delivered</th></tr>
                        </thead>
                        <tbody>
                            @foreach ($pesanan->details as $d)
                                <tr class="border-t">
                                    <td class="p-2">{{ $d->produk->nama }} ({{ $d->satuan }})</td>
                                    <td class="p-2">{{ $d->qty_pesan }}</td>
                                    <td class="p-2"><input type="number" step="0.01" min="0" name="qty[{{ $d->id }}]" value="{{ $d->qty_delivered ?? $d->qty_pesan }}" class="border rounded px-2 py-1 w-32" required /></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <button class="bg-green-700 text-white px-4 py-2 rounded">Simpan & Selesaikan</button>
                    <a href="{{ route('pesanan.index', ['tgl' => $pesanan->tgl_ambil->toDateString()]) }}" class="ms-3 text-gray-600 underline">Kembali</a>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
