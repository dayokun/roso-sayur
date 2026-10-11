<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-extrabold text-xl leading-tight" style="color: var(--rs-ink);">Prediksi</h2>
            <p class="text-xs" style="color: var(--rs-muted);">Rekomendasi pembelian berbasis Fuzzy Tsukamoto</p>
        </div>
    </x-slot>

    <div class="space-y-5">
        {{-- Page head --}}
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight" style="color: var(--rs-ink);">Prediksi stok besok</h1>
                <p class="text-sm mt-1" style="color: var(--rs-muted);">Rekomendasi pembelian untuk {{ \Carbon\Carbon::parse($tgl)->translatedFormat('l, d F Y') }}.</p>
            </div>
            @if ($hasil->isNotEmpty())
                <a href="{{ route('pembelian.create', ['tgl' => $tgl]) }}" class="rs-btn rs-btn-primary">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    Pakai semua rekomendasi
                </a>
            @endif
        </div>

        {{-- Filter tanggal --}}
        <div class="rs-card p-4">
            <form method="GET" action="{{ route('prediksi.index') }}" class="flex flex-wrap gap-3 items-end">
                <div>
                    <label class="rs-label">Tanggal prediksi</label>
                    <input type="date" name="tgl" value="{{ $tgl }}" class="rs-input !w-auto" />
                </div>
                <button class="rs-btn rs-btn-dark">Lihat</button>
            </form>
        </div>

        @if ($hasil->isNotEmpty())
            {{-- Info strip: metode + langkah --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <div class="lg:col-span-2 rounded-2xl p-5 flex gap-4 items-start" style="background: var(--rs-dark); color: #e5f1e8;">
                    <div class="w-11 h-11 rounded-xl grid place-items-center font-extrabold text-lg shrink-0" style="background: var(--rs-lime); color: #18391f;">ƒ</div>
                    <div>
                        <h3 class="font-bold text-sm">Fuzzy Tsukamoto · 27 aturan</h3>
                        <p class="text-xs mt-1 leading-relaxed" style="color: #a9bdaf;">Rekomendasi dihitung dari penjualan, histori 7 hari, dan tren. Safety buffer diterapkan sesuai kategori produk. Klik kartu untuk melihat rincian aturan.</p>
                    </div>
                </div>
                <div class="rs-card p-4 flex justify-around items-center">
                    <div class="text-center">
                        <span class="w-7 h-7 rounded-full grid place-items-center text-xs font-extrabold mx-auto mb-1" style="background: var(--rs-green); color: #fff;">1</span>
                        <span class="text-[10px] font-bold" style="color: var(--rs-muted);">Prediksi</span>
                    </div>
                    <div class="text-center opacity-40">
                        <span class="w-7 h-7 rounded-full grid place-items-center text-xs font-extrabold mx-auto mb-1" style="background: var(--rs-green-soft); color: var(--rs-green);">2</span>
                        <span class="text-[10px] font-bold" style="color: var(--rs-muted);">Pembelian</span>
                    </div>
                    <div class="text-center opacity-40">
                        <span class="w-7 h-7 rounded-full grid place-items-center text-xs font-extrabold mx-auto mb-1" style="background: var(--rs-green-soft); color: var(--rs-green);">3</span>
                        <span class="text-[10px] font-bold" style="color: var(--rs-muted);">Notifikasi</span>
                    </div>
                </div>
            </div>

            {{-- Grid kartu produk --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                @foreach ($hasil as $p)
                    @php
                        $isFuzzy = $p->details->isNotEmpty();
                        $buffer = $p->produk->kategori->safety_buffer_persen ?? 0;
                    @endphp
                    <details class="rs-card p-4 group cursor-pointer">
                        <summary class="list-none">
                            <div class="flex justify-between items-start gap-3">
                                <span class="w-10 h-10 rounded-xl grid place-items-center font-extrabold text-sm shrink-0"
                                      style="background: var(--rs-green-soft); color: var(--rs-green);">
                                    {{ strtoupper(substr($p->produk->nama, 0, 2)) }}
                                </span>
                                <span class="rs-badge {{ $isFuzzy ? 'info' : 'method' }}">{{ $isFuzzy ? 'fuzzy_tsukamoto' : 'seasonal_sum' }}</span>
                            </div>
                            <h3 class="font-bold text-sm mt-3" style="color: var(--rs-ink);">{{ $p->produk->nama }}</h3>
                            <p class="text-[10px]" style="color: var(--rs-muted);">{{ $p->produk->kategori->nama ?? '–' }} · Satuan {{ $p->produk->satuan }}</p>
                            <div class="flex items-end justify-between mt-4 pt-3 border-t" style="border-color: var(--rs-line);">
                                <div>
                                    <span class="text-[10px]" style="color: var(--rs-muted);">Rekomendasi beli</span>
                                    <div class="text-[10px] font-bold" style="color: var(--rs-amber);">+{{ number_format($buffer, 0) }}% safety buffer</div>
                                </div>
                                <strong class="text-2xl font-extrabold" style="color: var(--rs-ink);">
                                    {{ number_format($p->qty_dengan_buffer, 0) }}
                                    <small class="text-xs font-semibold" style="color: var(--rs-muted);">{{ $p->produk->satuan }}</small>
                                </strong>
                            </div>
                            <p class="text-[10px] mt-2 font-semibold group-open:hidden" style="color: var(--rs-green);">Klik untuk rincian aturan ▾</p>
                        </summary>
                        <div class="mt-3 pt-3 border-t text-xs space-y-1.5" style="border-color: var(--rs-line); color: var(--rs-muted);">
                            <p>Input — pesan: <strong style="color: var(--rs-ink);">{{ number_format($p->input_pesanan, 2) }}</strong>,
                               histori 7 hari: <strong style="color: var(--rs-ink);">{{ number_format($p->input_historis, 2) }}</strong>,
                               tren: <strong style="color: var(--rs-ink);">{{ number_format($p->input_tren, 4) }}</strong></p>
                            <p>Hasil fuzzy: <strong style="color: var(--rs-ink);">{{ number_format($p->qty_rekomendasi, 2) }} {{ $p->produk->satuan }}</strong></p>
                            @if ($isFuzzy)
                                <div class="overflow-x-auto -mx-1">
                                    <table class="w-full text-[10px] mt-2">
                                        <thead>
                                            <tr class="text-left uppercase" style="color: var(--rs-muted);">
                                                <th class="py-1 pr-2">Aturan</th><th class="py-1 pr-2">α min</th>
                                                <th class="py-1 pr-2">Output</th><th class="py-1 pr-2">z</th><th class="py-1">α×z</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($p->details->take(8) as $d)
                                                <tr class="border-t" style="border-color: var(--rs-line);">
                                                    <td class="py-1 pr-2 font-mono">{{ $d->rule_id }}</td>
                                                    <td class="py-1 pr-2">{{ number_format($d->alpha_value, 4) }}</td>
                                                    <td class="py-1 pr-2">{{ $d->output_term }}</td>
                                                    <td class="py-1 pr-2">{{ number_format($d->z_value, 2) }}</td>
                                                    <td class="py-1">{{ number_format($d->bobot, 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    @if ($p->details->count() > 8)
                                        <p class="mt-1 italic">+ {{ $p->details->count() - 8 }} aturan lainnya</p>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </details>
                @endforeach
            </div>
        @else
            <div class="rs-card p-10 text-center">
                <div class="w-14 h-14 rounded-2xl grid place-items-center mx-auto mb-4" style="background: var(--rs-amber-soft);">
                    <svg class="w-7 h-7" style="color: var(--rs-amber);" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="font-bold" style="color: var(--rs-ink);">Belum ada prediksi untuk tanggal ini</h3>
                <p class="text-sm mt-1" style="color: var(--rs-muted);">Prediksi otomatis jalan tiap jam 20:00 WIB.</p>
            </div>
        @endif
    </div>
</x-app-layout>
