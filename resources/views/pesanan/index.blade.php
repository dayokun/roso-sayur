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
                                <td class="p-2">{{ $p->status }}{{ $p->is_late_order ? ' (late)' : '' }}@if($p->status === 'cancelled' && $p->cancel_reason)<br><span class="text-xs text-gray-500">Alasan: {{ $p->cancel_reason }}</span>@endif</td>
                                <td class="p-2 whitespace-nowrap">
                                    @if ($p->status === 'pending')
                                        <form method="POST" action="{{ route('pesanan.terima', $p) }}" class="inline">@csrf<button class="text-blue-600 underline">Terima</button></form>
                                    @endif
                                    @if (in_array($p->status, ['diterima','siap_diambil']))
                                        <a href="{{ route('pesanan.delivered', $p) }}" class="text-green-600 underline">Input Delivered</a>
                                    @endif
                                    @if (! in_array($p->status, ['selesai','cancelled','siap_diambil']))
                                        <button type="button" onclick="bukaModalBatal('{{ route('pesanan.batal', $p) }}', '{{ $p->kode ?? $p->id }}')" class="text-red-600 underline">Batal</button>
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

    {{-- Modal konfirmasi pembatalan + alasan --}}
    <div id="modal-batal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden">
            <div class="bg-red-600 px-6 py-4">
                <h3 class="text-lg font-semibold text-white">Batalkan Pesanan</h3>
                <p class="text-red-100 text-sm">Kode: <span id="modal-batal-kode" class="font-mono font-semibold"></span></p>
            </div>
            <form id="modal-batal-form" method="POST" class="px-6 py-5">
                @csrf
                <label for="alasan-batal" class="block text-sm font-medium text-gray-700 mb-1">Alasan pembatalan <span class="text-red-600">*</span></label>
                <textarea id="alasan-batal" name="alasan" rows="3" required maxlength="255"
                    placeholder="Contoh: Stok habis, pesanan ganda, customer meminta batal..."
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm"></textarea>
                <div class="flex flex-wrap gap-2 mt-2">
                    <button type="button" onclick="isiAlasan('Stok produk habis')" class="text-xs px-3 py-1 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-700">Stok habis</button>
                    <button type="button" onclick="isiAlasan('Pesanan ganda')" class="text-xs px-3 py-1 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-700">Pesanan ganda</button>
                    <button type="button" onclick="isiAlasan('Customer meminta dibatalkan')" class="text-xs px-3 py-1 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-700">Customer meminta batal</button>
                </div>
                <p class="text-xs text-gray-500 mt-3">Customer akan menerima notifikasi WhatsApp berisi alasan ini.</p>
                <div class="flex justify-end gap-3 mt-5">
                    <button type="button" onclick="tutupModalBatal()" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm font-medium">Kembali</button>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white text-sm font-semibold">Ya, Batalkan</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        function bukaModalBatal(action, kode) {
            document.getElementById('modal-batal-form').action = action;
            document.getElementById('modal-batal-kode').textContent = kode;
            document.getElementById('alasan-batal').value = '';
            document.getElementById('modal-batal').classList.remove('hidden');
        }
        function tutupModalBatal() {
            document.getElementById('modal-batal').classList.add('hidden');
        }
        function isiAlasan(teks) {
            document.getElementById('alasan-batal').value = teks;
        }
        document.getElementById('modal-batal').addEventListener('click', function (e) {
            if (e.target === this) tutupModalBatal();
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') tutupModalBatal();
        });
    </script>
</x-app-layout>
