<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Prediksi — {{ $tgl }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <div class="bg-white shadow-sm sm:rounded-lg p-4">
                <form method="GET" action="{{ route('prediksi.index') }}" class="flex gap-3 items-end">
                    <div>
                        <label class="text-sm text-gray-600">Tanggal prediksi</label>
                        <input type="date" name="tgl" value="{{ $tgl }}" class="block border rounded px-2 py-1" />
                    </div>
                    <button class="bg-gray-800 text-white px-4 py-2 rounded">Lihat</button>
                </form>
            </div>

            @forelse ($hasil as $p)
                @php
                    $isFuzzy = $p->details->isNotEmpty();
                    $buffer = $p->produk->kategori->safety_buffer_persen ?? 0;
                @endphp
                <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                    <div class="p-4 border-b">
                        <div class="flex flex-wrap gap-4 items-center mb-2">
                            <strong>{{ $p->produk->nama }}</strong>
                            <span class="ms-auto text-xs px-2 py-1 rounded {{ $isFuzzy ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                {{ $isFuzzy ? 'fuzzy_tsukamoto' : 'seasonal_sum' }}
                            </span>
                        </div>
                        <div class="text-sm text-gray-600">
                            Input — pesan: {{ number_format($p->input_pesanan, 2) }},
                            histori 7 hari: {{ number_format($p->input_historis, 2) }},
                            tren: {{ number_format($p->input_tren, 4) }}
                        </div>
                        <div class="text-sm mt-1">
                            Rekomendasi fuzzy: <strong>{{ number_format($p->qty_rekomendasi, 2) }} {{ $p->produk->satuan }}</strong>
                            @if ($isFuzzy)
                                <span class="text-gray-600">+ buffer {{ number_format($buffer, 0) }}% →</span>
                            @endif
                            <strong>{{ number_format($p->qty_dengan_buffer, 2) }} {{ $p->produk->satuan }}</strong>
                        </div>
                    </div>
                    @if ($isFuzzy)
                        <table class="datatable w-full text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="p-2 text-left">Aturan</th>
                                    <th class="p-2 text-left">α pesan</th>
                                    <th class="p-2 text-left">α histori</th>
                                    <th class="p-2 text-left">α tren</th>
                                    <th class="p-2 text-left">α (min)</th>
                                    <th class="p-2 text-left">Output</th>
                                    <th class="p-2 text-left">z</th>
                                    <th class="p-2 text-left">α × z</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($p->details as $d)
                                    <tr class="border-t">
                                        <td class="p-2">{{ $d->rule_id }}</td>
                                        <td class="p-2">{{ number_format($d->alpha_pesanan, 4) }}</td>
                                        <td class="p-2">{{ number_format($d->alpha_historis, 4) }}</td>
                                        <td class="p-2">{{ number_format($d->alpha_tren, 4) }}</td>
                                        <td class="p-2">{{ number_format($d->alpha_value, 4) }}</td>
                                        <td class="p-2">{{ $d->output_term }}</td>
                                        <td class="p-2">{{ number_format($d->z_value, 2) }}</td>
                                        <td class="p-2">{{ number_format($d->bobot, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            @empty
                <div class="bg-white shadow-sm sm:rounded-lg p-6 text-center text-gray-500">
                    Belum ada prediksi untuk tanggal ini. Prediksi otomatis jalan tiap jam 20:00 WIB.
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
