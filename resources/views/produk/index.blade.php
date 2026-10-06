<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Produk & Kategori</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('sukses'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">{{ session('sukses') }}</div>
            @endif
            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">{{ session('error') }}</div>
            @endif
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    @foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-4 border-b font-semibold flex items-center">
                    Produk
                    <a href="{{ route('produk.create') }}" class="ms-auto bg-green-700 text-white px-4 py-2 rounded text-sm">Tambah Produk</a>
                </div>
                <table class="w-full text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="p-2 text-left">Nama</th>
                            <th class="p-2 text-left">Satuan</th>
                            <th class="p-2 text-left">Kategori</th>
                            <th class="p-2 text-left">Tersedia</th>
                            <th class="p-2 text-left">Seasonal</th>
                            <th class="p-2 text-left">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($produks as $p)
                            <tr class="border-t">
                                <td class="p-2">{{ $p->nama }}</td>
                                <td class="p-2">{{ $p->satuan }}</td>
                                <td class="p-2">{{ $p->kategori->nama_kategori }}</td>
                                <td class="p-2">{{ $p->is_available ? 'Ya' : 'Tidak' }}</td>
                                <td class="p-2">{{ $p->is_seasonal ? 'Ya' : 'Tidak' }}</td>
                                <td class="p-2 whitespace-nowrap">
                                    <a href="{{ route('produk.edit', $p) }}" class="text-blue-600 underline">Edit</a>
                                    <form method="POST" action="{{ route('produk.destroy', $p) }}" class="inline" onsubmit="return confirm('Hapus produk?')">@csrf @method('DELETE')<button class="text-red-600 underline ms-2">Hapus</button></form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-4 border-b font-semibold">Kategori & Safety Buffer</div>
                <table class="w-full text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="p-2 text-left">Kategori</th>
                            <th class="p-2 text-left">Safety Buffer (%)</th>
                            <th class="p-2 text-left">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($kategoris as $k)
                            <tr class="border-t">
                                <form method="POST" action="{{ route('kategori.update', $k) }}">@csrf @method('PUT')
                                    <td class="p-2"><input type="text" name="nama_kategori" value="{{ $k->nama_kategori }}" class="border rounded px-2 py-1 w-full" required /></td>
                                    <td class="p-2"><input type="number" step="0.01" min="0" max="15" name="safety_buffer_persen" value="{{ $k->safety_buffer_persen }}" class="border rounded px-2 py-1 w-28" required /></td>
                                    <td class="p-2"><button class="bg-blue-700 text-white px-3 py-1 rounded text-sm">Simpan</button></td>
                                </form>
                            </tr>
                        @endforeach
                        <tr class="border-t bg-gray-50">
                            <form method="POST" action="{{ route('kategori.store') }}">@csrf
                                <td class="p-2"><input type="text" name="nama_kategori" placeholder="Nama kategori baru" class="border rounded px-2 py-1 w-full" required /></td>
                                <td class="p-2"><input type="number" step="0.01" min="0" max="15" name="safety_buffer_persen" placeholder="%" class="border rounded px-2 py-1 w-28" required /></td>
                                <td class="p-2"><button class="bg-green-700 text-white px-3 py-1 rounded text-sm">Tambah</button></td>
                            </form>
                        </tr>
                    </tbody>
                </table>
                <p class="p-3 text-xs text-gray-500">Safety buffer maksimal 15% sesuai PRD.</p>
            </div>
        </div>
    </div>
</x-app-layout>
