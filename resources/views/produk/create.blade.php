<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tambah Produk</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        @foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach
                    </div>
                @endif
                <form method="POST" action="{{ route('produk.store') }}">
                    @csrf
                    <div class="mb-4">
                        <label class="text-sm text-gray-600">Nama produk</label>
                        <input type="text" name="nama" value="{{ old('nama') }}" class="block w-full border rounded px-2 py-1" required />
                    </div>
                    <div class="mb-4">
                        <label class="text-sm text-gray-600">Satuan</label>
                        <input type="text" name="satuan" value="{{ old('satuan') }}" placeholder="kg / ikat / sisir" class="block w-full border rounded px-2 py-1" required />
                    </div>
                    <div class="mb-4">
                        <label class="text-sm text-gray-600">Kategori</label>
                        <select name="kategori_id" class="block w-full border rounded px-2 py-1" required>
                            @foreach ($kategoris as $k)
                                <option value="{{ $k->id }}">{{ $k->nama_kategori }} (buffer {{ $k->safety_buffer_persen }}%)</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4 flex gap-6">
                        <label class="text-sm"><input type="checkbox" name="is_available" value="1" checked /> Tersedia</label>
                        <label class="text-sm"><input type="checkbox" name="is_seasonal" value="1" /> Seasonal (tanpa fuzzy)</label>
                    </div>
                    <button class="bg-green-700 text-white px-4 py-2 rounded">Simpan</button>
                    <a href="{{ route('produk.index') }}" class="ms-3 text-gray-600 underline">Kembali</a>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
