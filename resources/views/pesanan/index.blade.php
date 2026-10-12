<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-extrabold text-xl leading-tight" style="color: var(--rs-ink);">Pesanan</h2>
            <p class="text-xs" style="color: var(--rs-muted);">Kelola pesanan konsumen — {{ $tgl }}</p>
        </div>
    </x-slot>

    @php
        $statusBadge = fn($s) => match($s) {
            'pending' => 'pending', 'diterima' => 'diterima',
            'siap_diambil' => 'siap_diambil', 'selesai' => 'selesai',
            default => 'cancelled',
        };
        $statusLabel = fn($s) => match($s) {
            'pending' => 'Pending', 'diterima' => 'Diterima',
            'siap_diambil' => 'Siap diambil', 'selesai' => 'Selesai',
            'cancelled' => 'Dibatalkan', default => $s,
        };
    @endphp

    <div class="space-y-5">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight" style="color: var(--rs-ink);">Kelola pesanan</h1>
                <p class="text-sm mt-1" style="color: var(--rs-muted);">Pantau dan proses pesanan konsumen secara terpusat.</p>
            </div>
            <a href="{{ route('pesanan.late') }}" class="rs-btn rs-btn-amber">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Late Order
            </a>
        </div>

        <div class="rs-card p-4">
            <form method="GET" action="{{ route('pesanan.index') }}" class="flex flex-wrap gap-3 items-end">
                <div>
                    <label class="rs-label">Tanggal ambil</label>
                    <input type="date" name="tgl" value="{{ $tgl }}" class="rs-input !w-auto" />
                </div>
                <div>
                    <label class="rs-label">Status</label>
                    <select name="status" class="rs-input !w-auto">
                        <option value="">Semua</option>
                        @foreach (['pending','diterima','siap_diambil','selesai','cancelled'] as $s)
                            <option value="{{ $s }}" @selected($status === $s)>{{ $statusLabel($s) }}</option>
                        @endforeach
                    </select>
                </div>
                <button class="rs-btn rs-btn-dark">Filter</button>
            </form>
        </div>

        <div class="rs-card p-5">
            <table class="datatable w-full text-sm">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Konsumen</th>
                        <th>Item</th>
                        <th>Tgl Pesan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pesanans as $p)
                        <tr>
                            <td class="font-mono font-bold whitespace-nowrap" style="color: var(--rs-green);">{{ $p->kode ?? $p->id }}</td>
                            <td>
                                <p class="font-semibold">{{ $p->konsumen->nama }}</p>
                                <p class="text-xs" style="color: var(--rs-muted);">{{ $p->konsumen->no_hp }}</p>
                            </td>
                            <td class="text-xs">
                                @foreach ($p->details as $d)
                                    {{ $d->produk->nama }}: {{ $d->qty_pesan }} {{ $d->satuan }}@if(!is_null($d->qty_delivered)) → {{ $d->qty_delivered }}@endif<br>
                                @endforeach
                            </td>
                            <td class="whitespace-nowrap text-xs">{{ $p->tgl_pesan->format('d/m H:i') }}</td>
                            <td>
                                <span class="rs-badge {{ $statusBadge($p->status) }}">{{ $statusLabel($p->status) }}</span>
                                @if($p->is_late_order)<span class="rs-badge pending ms-1">late</span>@endif
                                @if($p->status === 'cancelled' && $p->cancel_reason)
                                    <p class="text-[10px] mt-1" style="color: var(--rs-muted);">Alasan: {{ $p->cancel_reason }}</p>
                                @endif
                            </td>
                            <td class="whitespace-nowrap">
                                <div class="flex flex-wrap gap-1.5">
                                    @if ($p->status === 'pending')
                                        <form method="POST" action="{{ route('pesanan.terima', $p) }}" class="inline">@csrf
                                            <button class="rs-btn rs-btn-secondary !py-1.5 !px-3 !text-[11px]">Terima</button>
                                        </form>
                                    @endif
                                    @if (in_array($p->status, ['diterima','siap_diambil']))
                                        <a href="{{ route('pesanan.delivered', $p) }}" class="rs-btn rs-btn-primary !py-1.5 !px-3 !text-[11px]">Input Delivered</a>
                                    @endif
                                    @if (! in_array($p->status, ['selesai','cancelled','siap_diambil']))
                                        <button type="button" onclick="bukaModalBatal('{{ route('pesanan.batal', $p) }}', '{{ $p->kode ?? $p->id }}')" class="rs-btn rs-btn-danger !py-1.5 !px-3 !text-[11px]">Batal</button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="p-4 text-center" style="color: var(--rs-muted);">Tidak ada pesanan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal konfirmasi pembatalan + alasan --}}
    <div id="modal-batal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div class="rounded-2xl shadow-xl w-full max-w-md overflow-hidden" style="background: var(--rs-surface);">
            <div class="px-6 py-4" style="background: var(--rs-red);">
                <h3 class="text-lg font-bold text-white">Batalkan Pesanan</h3>
                <p class="text-sm" style="color: #fbd5d5;">Kode: <span id="modal-batal-kode" class="font-mono font-bold"></span></p>
            </div>
            <form id="modal-batal-form" method="POST" class="px-6 py-5">
                @csrf
                <label for="alasan-batal" class="rs-label">Alasan pembatalan <span style="color: var(--rs-red);">*</span></label>
                <textarea id="alasan-batal" name="alasan" rows="3" required maxlength="255"
                    placeholder="Contoh: Stok habis, pesanan ganda, customer meminta batal..."
                    class="rs-textarea"></textarea>
                <div class="flex flex-wrap gap-2 mt-2">
                    <button type="button" onclick="isiAlasan('Stok produk habis')" class="text-xs px-3 py-1 rounded-full font-semibold transition hover:opacity-80" style="background: var(--rs-surface-2); color: var(--rs-muted);">Stok habis</button>
                    <button type="button" onclick="isiAlasan('Pesanan ganda')" class="text-xs px-3 py-1 rounded-full font-semibold transition hover:opacity-80" style="background: var(--rs-surface-2); color: var(--rs-muted);">Pesanan ganda</button>
                    <button type="button" onclick="isiAlasan('Customer meminta dibatalkan')" class="text-xs px-3 py-1 rounded-full font-semibold transition hover:opacity-80" style="background: var(--rs-surface-2); color: var(--rs-muted);">Customer meminta batal</button>
                </div>
                <p class="text-xs mt-3" style="color: var(--rs-muted);">Customer akan menerima notifikasi WhatsApp berisi alasan ini.</p>
                <div class="flex justify-end gap-3 mt-5">
                    <button type="button" onclick="tutupModalBatal()" class="rs-btn rs-btn-secondary">Kembali</button>
                    <button type="submit" class="rs-btn text-white font-bold" style="background: var(--rs-red);">Ya, Batalkan</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
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
    @endpush
</x-app-layout>
