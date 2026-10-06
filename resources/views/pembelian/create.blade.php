<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Input Actual Pembelian — {{ $tgl }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        @foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('pembelian.store') }}">
                    @csrf
                    <input type="hidden" name="tgl" value="{{ $tgl }}" />
                    <table class="w-full text-sm mb-4">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="p-2 text-left">Produk</th>
                                <th class="p-2 text-left">Rekomendasi</th>
                                <th class="p-2 text-left">Tersedia?</th>
                                <th class="p-2 text-left">Qty Beli</th>
                                <th class="p-2 text-left">Harga Beli (Rp)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($produks as $i => $p)
                                @php $row = $sudah[$p->id] ?? null; @endphp
                                <tr class="border-t">
                                    <td class="p-2">
                                        {{ $p->nama }} ({{ $p->satuan }})
                                        <input type="hidden" name="items[{{ $i }}][produk_id]" value="{{ $p->id }}" />
                                    </td>
                                    <td class="p-2 text-gray-600">{{ isset($rekomendasi[$p->id]) ? number_format($rekomendasi[$p->id], 2) . ' ' . $p->satuan : '-' }}</td>
                                    <td class="p-2">
                                        <input type="checkbox" name="items[{{ $i }}][is_available_today]" value="1" @checked(old("items.$i.is_available_today", $row?->is_available_today ?? true)) />
                                    </td>
                                    <td class="p-2"><input type="number" step="0.01" min="0" name="items[{{ $i }}][qty_beli]" value="{{ old("items.$i.qty_beli", $row?->qty_beli) }}" class="border rounded px-2 py-1 w-28" /></td>
                                    <td class="p-2"><input type="number" step="0.01" min="0" name="items[{{ $i }}][harga_beli]" value="{{ old("items.$i.harga_beli", $row?->harga_beli) }}" class="border rounded px-2 py-1 w-32" /></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mb-4">
                        <label class="text-sm text-gray-600">Catatan</label>
                        <textarea name="catatan" class="block w-full border rounded px-2 py-1" rows="2"></textarea>
                    </div>
                    <button class="bg-green-700 text-white px-4 py-2 rounded">Simpan</button>
                    <a href="{{ route('pembelian.index', ['tgl' => $tgl]) }}" class="ms-3 text-gray-600 underline">Kembali</a>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
