<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-extrabold text-xl leading-tight" style="color: var(--rs-ink);">Input Pembelian</h2>
            <p class="text-xs" style="color: var(--rs-muted);">Actual pembelian — {{ $tgl }}</p>
        </div>
    </x-slot>

    <div class="space-y-5">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight" style="color: var(--rs-ink);">Input pembelian</h1>
                <p class="text-sm mt-1" style="color: var(--rs-muted);">Isi jumlah aktual dan harga beli dari pemasok.</p>
            </div>
            @if (!$rekomendasi->isEmpty())
                <button type="button" id="btn-pakai-rekomendasi" class="rs-btn rs-btn-secondary">
                    Pakai semua rekomendasi
                </button>
            @endif
        </div>

        <div class="rs-card p-5">
            <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                <div>
                    <h2 class="font-bold text-base" style="color: var(--rs-ink);">Pembelian {{ \Carbon\Carbon::parse($tgl)->translatedFormat('d F Y') }}</h2>
                    <p class="text-xs" style="color: var(--rs-muted);">Klik angka rekomendasi untuk mengisi jumlah aktual</p>
                </div>
                <span class="rs-badge pending" id="status-simpan">Belum disimpan</span>
            </div>

            @if ($errors->any())
                <div class="rounded-xl px-4 py-3 mb-4 text-sm font-semibold" style="background: var(--rs-red-soft); color: var(--rs-red);">
                    @foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach
                </div>
            @endif

            @if ($rekomendasi->isEmpty())
                <div class="rounded-xl px-4 py-3 mb-4 text-sm" style="background: var(--rs-amber-soft); color: var(--rs-amber);">
                    Belum ada prediksi untuk tanggal {{ $tgl }}. Prediksi dibuat otomatis setiap pukul 20:00 untuk keesokan harinya —
                    angka rekomendasi akan muncul di sini setelah prediksi tersedia.
                </div>
            @endif

            <form method="POST" action="{{ route('pembelian.store') }}" id="form-pembelian">
                @csrf
                <input type="hidden" name="tgl" value="{{ $tgl }}" />
                <div class="overflow-x-auto -mx-1">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-[10px] uppercase tracking-wider" style="color: var(--rs-muted);">
                                <th class="pb-2 pr-3 font-bold">Produk</th>
                                <th class="pb-2 pr-3 font-bold">Rekomendasi</th>
                                <th class="pb-2 pr-3 font-bold">Tersedia?</th>
                                <th class="pb-2 pr-3 font-bold">Qty Beli</th>
                                <th class="pb-2 pr-3 font-bold">Harga Beli (Rp)</th>
                                <th class="pb-2 font-bold text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($produks as $i => $p)
                                @php $row = $sudah[$p->id] ?? null; @endphp
                                <tr class="border-t" style="border-color: var(--rs-line);">
                                    <td class="py-3 pr-3">
                                        <div class="flex items-center gap-2.5">
                                            <span class="w-8 h-8 rounded-lg grid place-items-center font-extrabold text-[11px] shrink-0"
                                                  style="background: var(--rs-green-soft); color: var(--rs-green);">
                                                {{ strtoupper(substr($p->nama, 0, 2)) }}
                                            </span>
                                            <div>
                                                <p class="font-bold text-xs" style="color: var(--rs-ink);">{{ $p->nama }}</p>
                                                <p class="text-[10px]" style="color: var(--rs-muted);">{{ $p->satuan }}</p>
                                            </div>
                                        </div>
                                        <input type="hidden" name="items[{{ $i }}][produk_id]" value="{{ $p->id }}" />
                                    </td>
                                    <td class="py-3 pr-3">
                                        @if (isset($rekomendasi[$p->id]))
                                            <button type="button" class="btn-pakai-satu font-extrabold text-xs px-2.5 py-1.5 rounded-lg transition hover:opacity-80 active:scale-95"
                                                    style="background: var(--rs-green-soft); color: var(--rs-green);"
                                                    data-produk="{{ $p->id }}" data-idx="{{ $i }}"
                                                    title="Isi qty beli dengan rekomendasi">
                                                {{ number_format($rekomendasi[$p->id], 2) }} {{ $p->satuan }}
                                            </button>
                                        @else
                                            <span style="color: var(--rs-muted);">–</span>
                                        @endif
                                    </td>
                                    <td class="py-3 pr-3">
                                        <input type="checkbox" name="items[{{ $i }}][is_available_today]" value="1"
                                               @checked(old("items.$i.is_available_today", $row?->is_available_today ?? true))
                                               class="w-4 h-4 rounded accent-green-700" />
                                    </td>
                                    <td class="py-3 pr-3">
                                        <input type="number" step="0.01" min="0" name="items[{{ $i }}][qty_beli]"
                                               value="{{ old("items.$i.qty_beli", $row?->qty_beli) }}"
                                               class="rs-input qty-beli !w-28" data-idx="{{ $i }}" placeholder="0" />
                                    </td>
                                    <td class="py-3 pr-3">
                                        <input type="number" step="0.01" min="0" name="items[{{ $i }}][harga_beli]"
                                               value="{{ old("items.$i.harga_beli", $row?->harga_beli) }}"
                                               class="rs-input harga-beli !w-36" data-idx="{{ $i }}" placeholder="Rp0" />
                                    </td>
                                    <td class="py-3 text-right font-bold text-xs whitespace-nowrap subtotal" data-idx="{{ $i }}" style="color: var(--rs-ink);">Rp0</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    <label class="rs-label">Catatan</label>
                    <textarea name="catatan" class="rs-textarea" rows="2" placeholder="Catatan pembelian (opsional)">{{ old('catatan') }}</textarea>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-4 mt-5 pt-4 border-t" style="border-color: var(--rs-line);">
                    <a href="{{ route('pembelian.index', ['tgl' => $tgl]) }}" class="text-sm font-bold underline" style="color: var(--rs-muted);">← Kembali</a>
                    <div class="flex items-center gap-4">
                        <span class="text-sm" style="color: var(--rs-muted);">Total estimasi <strong id="total-estimasi" class="text-lg font-extrabold" style="color: var(--rs-ink);">Rp0</strong></span>
                        <button type="submit" class="rs-btn rs-btn-primary">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            Simpan pembelian
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        const rekomendasi = @json($rekomendasi->map(fn($v) => (float) $v));
        const fmtRp = n => 'Rp' + Math.round(n).toLocaleString('id-ID');

        function hitungSubtotal() {
            let total = 0;
            document.querySelectorAll('.qty-beli').forEach(qtyEl => {
                const idx = qtyEl.dataset.idx;
                const qty = parseFloat(qtyEl.value) || 0;
                const hargaEl = document.querySelector(`.harga-beli[data-idx="${idx}"]`);
                const harga = parseFloat(hargaEl?.value) || 0;
                const sub = qty * harga;
                const subEl = document.querySelector(`.subtotal[data-idx="${idx}"]`);
                if (subEl) subEl.textContent = fmtRp(sub);
                total += sub;
            });
            document.getElementById('total-estimasi').textContent = fmtRp(total);
        }

        function isiQty(idx, produkId) {
            const val = rekomendasi[produkId];
            if (val === undefined || val === null) return;
            const input = document.querySelector(`input[name="items[${idx}][qty_beli]"]`);
            if (input) { input.value = val; hitungSubtotal(); }
        }

        document.getElementById('btn-pakai-rekomendasi')?.addEventListener('click', () => {
            document.querySelectorAll('.btn-pakai-satu').forEach(btn => isiQty(btn.dataset.idx, btn.dataset.produk));
            rsToast('Semua rekomendasi disalin ke input aktual');
        });

        document.querySelectorAll('.btn-pakai-satu').forEach(btn => {
            btn.addEventListener('click', () => isiQty(btn.dataset.idx, btn.dataset.produk));
        });

        document.querySelectorAll('.qty-beli, .harga-beli').forEach(el => {
            el.addEventListener('input', hitungSubtotal);
        });

        document.getElementById('form-pembelian')?.addEventListener('submit', () => {
            rsToast('Menyimpan pembelian…');
        });

        hitungSubtotal();
    </script>
    @endpush
</x-app-layout>
