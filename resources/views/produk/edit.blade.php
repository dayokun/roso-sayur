<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Produk — {{ $produk->nama }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        @foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach
                    </div>
                @endif
                <form method="POST" action="{{ route('produk.update', $produk) }}">
                    @csrf @method('PUT')
                    <div class="mb-4">
                        <label class="text-sm text-gray-600">Nama produk</label>
                        <input type="text" name="nama" value="{{ old('nama', $produk->nama) }}" class="block w-full border rounded px-2 py-1" required />
                    </div>
                    <div class="mb-4">
                        <label class="text-sm text-gray-600">Satuan</label>
                        <input type="text" name="satuan" value="{{ old('satuan', $produk->satuan) }}" class="block w-full border rounded px-2 py-1" required />
                    </div>
                    <div class="mb-4">
                        <label class="text-sm text-gray-600">Kategori</label>
                        <select name="kategori_id" class="block w-full border rounded px-2 py-1" required>
                            @foreach ($kategoris as $k)
                                <option value="{{ $k->id }}" @selected(old('kategori_id', $produk->kategori_id) == $k->id)>{{ $k->nama_kategori }} (buffer {{ $k->safety_buffer_persen }}%)</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4 flex gap-6">
                        <label class="text-sm"><input type="checkbox" name="is_available" value="1" @checked(old('is_available', $produk->is_available)) /> Tersedia</label>
                        <label class="text-sm"><input type="checkbox" name="is_seasonal" value="1" @checked(old('is_seasonal', $produk->is_seasonal)) /> Seasonal (tanpa fuzzy)</label>
                    </div>
                    <button class="bg-blue-700 text-white px-4 py-2 rounded">Simpan</button>
                    <a href="{{ route('produk.index') }}" class="ms-3 text-gray-600 underline">Kembali</a>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
