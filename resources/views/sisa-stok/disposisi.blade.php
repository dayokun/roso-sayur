<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Disposisi Sisa Stok</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        @foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach
                    </div>
                @endif
                <p class="mb-4 text-gray-700">
                    Produk: <strong>{{ $sisa->pembelianDetail->produk->nama }}</strong><br>
                    Qty sisa: <strong>{{ number_format($sisa->qty_sisa, 2) }} {{ $sisa->pembelianDetail->produk->satuan }}</strong>
                </p>
                <form method="POST" action="{{ route('sisa-stok.disposisi.store', $sisa->id) }}">
                    @csrf
                    <div class="mb-4">
                        <label class="text-sm text-gray-600">Disposisi</label>
                        <select name="status_sisa" class="block w-full border rounded px-2 py-1" required>
                            <option value="dijual_murah" @selected(old('status_sisa', $sisa->status_sisa) === 'dijual_murah')>Dijual murah</option>
                            <option value="dibuang" @selected(old('status_sisa', $sisa->status_sisa) === 'dibuang')>Dibuang</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="text-sm text-gray-600">Harga jual murah (Rp)</label>
                        <input type="number" step="0.01" min="0" name="harga_jual_murah" value="{{ old('harga_jual_murah', $sisa->harga_jual_murah) }}" class="block w-full border rounded px-2 py-1" />
                    </div>
                    <div class="mb-4">
                        <label class="text-sm text-gray-600">Qty terjual murah</label>
                        <input type="number" step="0.01" min="0" name="qty_terjual_murah" value="{{ old('qty_terjual_murah', $sisa->qty_terjual_murah) }}" class="block w-full border rounded px-2 py-1" />
                    </div>
                    <div class="mb-4">
                        <label class="text-sm text-gray-600">Qty dibuang</label>
                        <input type="number" step="0.01" min="0" name="qty_dibuang" value="{{ old('qty_dibuang', $sisa->qty_dibuang) }}" class="block w-full border rounded px-2 py-1" />
                    </div>
                    <button class="bg-green-700 text-white px-4 py-2 rounded">Simpan</button>
                    <a href="{{ route('sisa-stok.index', ['tgl' => $sisa->tgl->toDateString()]) }}" class="ms-3 text-gray-600 underline">Kembali</a>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
