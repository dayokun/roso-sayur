<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Laporan — {{ $dari }} s/d {{ $sampai }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <div class="bg-white shadow-sm sm:rounded-lg p-4">
                <form method="GET" action="{{ route('laporan.index') }}" class="flex gap-3 items-end">
                    <div>
                        <label class="text-sm text-gray-600">Dari</label>
                        <input type="date" name="dari" value="{{ $dari }}" class="block border rounded px-2 py-1" />
                    </div>
                    <div>
                        <label class="text-sm text-gray-600">Sampai</label>
                        <input type="date" name="sampai" value="{{ $sampai }}" class="block border rounded px-2 py-1" />
                    </div>
                    <button class="bg-gray-800 text-white px-4 py-2 rounded">Tampilkan</button>
                </form>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-4 border-b font-semibold">Akurasi Prediksi (MAPE / MAE)</div>
                <table class="w-full text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="p-2 text-left">Produk</th>
                            <th class="p-2 text-left">N</th>
                            <th class="p-2 text-left">Rata-rata MAPE</th>
                            <th class="p-2 text-left">Rata-rata MAE</th>
                            <th class="p-2 text-left">Status Target</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($ringkasan['akurasi'] as $r)
                            <tr class="border-t">
                                <td class="p-2">{{ $r->nama }}</td>
                                <td class="p-2">{{ $r->n }}</td>
                                <td class="p-2">{{ $r->avg_mape !== null ? number_format($r->avg_mape, 2) . '%' : '-' }}</td>
                                <td class="p-2">{{ $r->avg_mae !== null ? number_format($r->avg_mae, 2) : '-' }}</td>
                                <td class="p-2">
                                    @if ($r->avg_mape !== null)
                                        <span class="{{ $r->avg_mape < 20 ? 'text-green-700 font-semibold' : 'text-red-700 font-semibold' }}">
                                            {{ $r->avg_mape < 20 ? 'Memenuhi (<20%)' : 'Belum memenuhi' }}
                                        </span>
                                    @else
                                        <span class="text-gray-500">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="p-4 text-center text-gray-500">Belum ada data evaluasi.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-4 border-b font-semibold">Fulfillment Rate (delivered / dipesan)</div>
                <table class="w-full text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="p-2 text-left">Produk</th>
                            <th class="p-2 text-left">Total Pesan</th>
                            <th class="p-2 text-left">Total Delivered</th>
                            <th class="p-2 text-left">Fulfillment</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($ringkasan['fulfillment'] as $f)
                            @php $rate = $f->total_pesan > 0 ? $f->total_delivered / $f->total_pesan * 100 : null; @endphp
                            <tr class="border-t">
                                <td class="p-2">{{ $f->nama }}</td>
                                <td class="p-2">{{ number_format($f->total_pesan, 2) }}</td>
                                <td class="p-2">{{ number_format($f->total_delivered, 2) }}</td>
                                <td class="p-2">
                                    @if ($rate !== null)
                                        <span class="{{ $rate > 95 ? 'text-green-700 font-semibold' : 'text-amber-700 font-semibold' }}">{{ number_format($rate, 1) }}%</span>
                                    @else
                                        <span class="text-gray-500">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="p-4 text-center text-gray-500">Belum ada data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
