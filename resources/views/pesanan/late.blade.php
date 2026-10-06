<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Late Order — Menunggu Approval</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('sukses'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">{{ session('sukses') }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="p-2 text-left">#</th>
                            <th class="p-2 text-left">Konsumen</th>
                            <th class="p-2 text-left">Item</th>
                            <th class="p-2 text-left">Masuk</th>
                            <th class="p-2 text-left">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pesanans as $p)
                            <tr class="border-t">
                                <td class="p-2">{{ $p->id }}</td>
                                <td class="p-2">{{ $p->konsumen->nama }}<br><span class="text-gray-500">{{ $p->konsumen->no_hp }}</span></td>
                                <td class="p-2">
                                    @foreach ($p->details as $d)
                                        {{ $d->produk->nama }}: {{ $d->qty_pesan }} {{ $d->satuan }}<br>
                                    @endforeach
                                </td>
                                <td class="p-2">{{ $p->tgl_pesan->diffForHumans() }}</td>
                                <td class="p-2 whitespace-nowrap">
                                    <form method="POST" action="{{ route('pesanan.late-terima', $p) }}" class="inline">@csrf<button class="bg-green-700 text-white px-3 py-1 rounded">Terima</button></form>
                                    <form method="POST" action="{{ route('pesanan.late-tolak', $p) }}" class="inline">@csrf
                                        <select name="alasan" class="border rounded px-1 py-1 text-sm">
                                            <option value="stok_habis">Stok habis</option>
                                            <option value="terlambat_pesan">Terlambat pesan</option>
                                        </select>
                                        <button class="bg-red-700 text-white px-3 py-1 rounded">Tolak</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="p-4 text-center text-gray-500">Tidak ada late order menunggu.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="p-4">{{ $pesanans->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
