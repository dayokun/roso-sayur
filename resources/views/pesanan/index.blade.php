<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Pesanan — {{ $tgl }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('sukses'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">{{ session('sukses') }}</div>
            @endif
            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">{{ session('error') }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-4 flex gap-3 items-end">
                <form method="GET" action="{{ route('pesanan.index') }}" class="flex gap-3 items-end">
                    <div>
                        <label class="text-sm text-gray-600">Tanggal ambil</label>
                        <input type="date" name="tgl" value="{{ $tgl }}" class="block border rounded px-2 py-1" />
                    </div>
                    <div>
                        <label class="text-sm text-gray-600">Status</label>
                        <select name="status" class="block border rounded px-2 py-1">
                            <option value="">Semua</option>
                            @foreach (['pending','diterima','siap_diambil','selesai','cancelled'] as $s)
                                <option value="{{ $s }}" @selected($status === $s)>{{ $s }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button class="bg-gray-800 text-white px-4 py-2 rounded">Filter</button>
                </form>
                <a href="{{ route('pesanan.late') }}" class="ms-auto bg-amber-600 text-white px-4 py-2 rounded">Late Order</a>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <table class="datatable w-full text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="p-2 text-left">Kode</th>
                            <th class="p-2 text-left">Konsumen</th>
                            <th class="p-2 text-left">Item</th>
                            <th class="p-2 text-left">Tgl Pesan</th>
                            <th class="p-2 text-left">Status</th>
                            <th class="p-2 text-left">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pesanans as $p)
                            <tr class="border-t">
                                <td class="p-2 font-mono font-semibold text-green-800 whitespace-nowrap">{{ $p->kode ?? $p->id }}</td>
                                <td class="p-2">{{ $p->konsumen->nama }}<br><span class="text-gray-500">{{ $p->konsumen->no_hp }}</span></td>
                                <td class="p-2">
                                    @foreach ($p->details as $d)
                                        {{ $d->produk->nama }}: {{ $d->qty_pesan }} {{ $d->satuan }}@if(!is_null($d->qty_delivered)) → {{ $d->qty_delivered }}@endif<br>
                                    @endforeach
                                </td>
                                <td class="p-2">{{ $p->tgl_pesan->format('d/m H:i') }}</td>
                                <td class="p-2">{{ $p->status }}{{ $p->is_late_order ? ' (late)' : '' }}</td>
                                <td class="p-2 whitespace-nowrap">
                                    @if ($p->status === 'pending')
                                        <form method="POST" action="{{ route('pesanan.terima', $p) }}" class="inline">@csrf<button class="text-blue-600 underline">Terima</button></form>
                                    @endif
                                    @if (in_array($p->status, ['diterima','siap_diambil']))
                                        <a href="{{ route('pesanan.delivered', $p) }}" class="text-green-600 underline">Input Delivered</a>
                                    @endif
                                    @if (! in_array($p->status, ['selesai','cancelled','siap_diambil']))
                                        <form method="POST" action="{{ route('pesanan.batal', $p) }}" class="inline" onsubmit="return confirm('Batalkan pesanan?')">@csrf<button class="text-red-600 underline">Batal</button></form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="p-4 text-center text-gray-500">Tidak ada pesanan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
