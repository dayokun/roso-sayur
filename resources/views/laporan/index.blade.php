<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-extrabold text-xl leading-tight" style="color: var(--rs-ink);">Laporan</h2>
            <p class="text-xs" style="color: var(--rs-muted);">Evaluasi prediksi & performa operasional</p>
        </div>
    </x-slot>

    @php
        // Ringkasan metrik untuk kartu score
        $mapeVals = collect($ringkasan['akurasi'] ?? [])->filter(fn($r) => $r->avg_mape !== null);
        $avgMape = $mapeVals->count() ? $mapeVals->avg('avg_mape') : null;
        $akurasi = $avgMape !== null ? max(0, 100 - $avgMape) : null;

        $fulVals = collect($ringkasan['fulfillment'] ?? [])->filter(fn($f) => $f->total_pesan > 0);
        $avgFul = $fulVals->count()
            ? $fulVals->sum(fn($f) => $f->total_delivered) / max($fulVals->sum(fn($f) => $f->total_pesan), 1) * 100
            : null;

        $wasteVals = collect($ringkasan['waste_rate'] ?? [])->filter(fn($w) => $w->total_beli > 0);
        $avgWaste = $wasteVals->count()
            ? $wasteVals->sum(fn($w) => $w->total_dibuang) / max($wasteVals->sum(fn($w) => $w->total_beli), 1) * 100
            : null;
    @endphp

    <div class="space-y-5">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight" style="color: var(--rs-ink);">Laporan & evaluasi</h1>
                <p class="text-sm mt-1" style="color: var(--rs-muted);">Bandingkan prediksi, realisasi, dan performa operasional.</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('laporan.export-excel', ['dari' => $dari, 'sampai' => $sampai]) }}" class="rs-btn rs-btn-secondary">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v12m-5-5l5 5 5-5M4 20h16"/></svg>
                    Export Excel
                </a>
                <a href="{{ route('laporan.export-pdf', ['dari' => $dari, 'sampai' => $sampai]) }}" class="rs-btn rs-btn-primary">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v12m-5-5l5 5 5-5M4 20h16"/></svg>
                    Export PDF
                </a>
            </div>
        </div>

        {{-- Filter periode --}}
        <div class="rs-card p-4">
            <form method="GET" action="{{ route('laporan.index') }}" class="flex flex-wrap gap-3 items-end">
                <div>
                    <label class="rs-label">Dari</label>
                    <input type="date" name="dari" value="{{ $dari }}" class="rs-input !w-auto" />
                </div>
                <div>
                    <label class="rs-label">Sampai</label>
                    <input type="date" name="sampai" value="{{ $sampai }}" class="rs-input !w-auto" />
                </div>
                <button class="rs-btn rs-btn-dark">Tampilkan</button>
                <span class="ms-auto text-xs font-semibold" style="color: var(--rs-muted);">{{ $dari }} s/d {{ $sampai }}</span>
            </form>
        </div>

        {{-- Kartu metrik dengan score ring --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="rs-card p-5 flex justify-between items-center gap-4">
                <div>
                    <h3 class="text-xs font-semibold" style="color: var(--rs-muted);">Akurasi prediksi</h3>
                    <p class="text-2xl font-extrabold mt-1" style="color: var(--rs-ink);">{{ $akurasi !== null ? number_format($akurasi, 1) . '%' : '–' }}</p>
                    <span class="text-xs" style="color: var(--rs-muted);">MAPE {{ $avgMape !== null ? number_format($avgMape, 1) . '%' : '–' }} (target &lt;20%)</span>
                </div>
                <div class="rs-score-ring" style="--rs-score: {{ $akurasi ?? 0 }}%;"><b>{{ $akurasi !== null && $avgMape < 20 ? 'Baik' : ($akurasi !== null ? number_format($akurasi, 0) . '%' : '–') }}</b></div>
            </div>
            <div class="rs-card p-5 flex justify-between items-center gap-4">
                <div>
                    <h3 class="text-xs font-semibold" style="color: var(--rs-muted);">Fulfillment rate</h3>
                    <p class="text-2xl font-extrabold mt-1" style="color: var(--rs-ink);">{{ $avgFul !== null ? number_format($avgFul, 1) . '%' : '–' }}</p>
                    <span class="text-xs" style="color: var(--rs-muted);">Target &gt;95%</span>
                </div>
                <div class="rs-score-ring" style="--rs-score: {{ $avgFul ?? 0 }}%;"><b>{{ $avgFul !== null ? number_format($avgFul, 0) . '%' : '–' }}</b></div>
            </div>
            <div class="rs-card p-5 flex justify-between items-center gap-4">
                <div>
                    <h3 class="text-xs font-semibold" style="color: var(--rs-muted);">Waste rate</h3>
                    <p class="text-2xl font-extrabold mt-1" style="color: var(--rs-ink);">{{ $avgWaste !== null ? number_format($avgWaste, 1) . '%' : '–' }}</p>
                    <span class="text-xs" style="color: var(--rs-muted);">Target &lt;10%</span>
                </div>
                <div class="rs-score-ring" style="--rs-score: {{ $avgWaste !== null ? min(100, $avgWaste * 10) : 0 }}%;"><b>{{ $avgWaste !== null ? number_format($avgWaste, 1) . '%' : '–' }}</b></div>
            </div>
        </div>

        {{-- Tabel akurasi --}}
        <div class="rs-card p-5">
            <h2 class="font-bold text-base mb-4" style="color: var(--rs-ink);">Akurasi Prediksi (MAPE / MAE)</h2>
            <table class="datatable w-full text-sm">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>N</th>
                        <th>Rata-rata MAPE</th>
                        <th>Rata-rata MAE</th>
                        <th>Status Target</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($ringkasan['akurasi'] as $r)
                        <tr>
                            <td class="font-semibold">{{ $r->nama }}</td>
                            <td>{{ $r->n }}</td>
                            <td>{{ $r->avg_mape !== null ? number_format($r->avg_mape, 2) . '%' : '-' }}</td>
                            <td>{{ $r->avg_mae !== null ? number_format($r->avg_mae, 2) : '-' }}</td>
                            <td>
                                @if ($r->avg_mape !== null)
                                    <span class="rs-badge {{ $r->avg_mape < 20 ? 'siap_diambil' : 'cancelled' }}">
                                        {{ $r->avg_mape < 20 ? 'Memenuhi (<20%)' : 'Belum memenuhi' }}
                                    </span>
                                @else
                                    <span style="color: var(--rs-muted);">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="p-4 text-center" style="color: var(--rs-muted);">Belum ada data evaluasi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Tabel fulfillment --}}
        <div class="rs-card p-5">
            <h2 class="font-bold text-base mb-4" style="color: var(--rs-ink);">Fulfillment Rate (delivered / dipesan)</h2>
            <table class="datatable w-full text-sm">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Total Pesan</th>
                        <th>Total Delivered</th>
                        <th>Fulfillment</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($ringkasan['fulfillment'] as $f)
                        @php $rate = $f->total_pesan > 0 ? $f->total_delivered / $f->total_pesan * 100 : null; @endphp
                        <tr>
                            <td class="font-semibold">{{ $f->nama }}</td>
                            <td>{{ number_format($f->total_pesan, 2) }}</td>
                            <td>{{ number_format($f->total_delivered, 2) }}</td>
                            <td>
                                @if ($rate !== null)
                                    <span class="rs-badge {{ $rate > 95 ? 'siap_diambil' : 'pending' }}">{{ number_format($rate, 1) }}%</span>
                                @else
                                    <span style="color: var(--rs-muted);">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="p-4 text-center" style="color: var(--rs-muted);">Belum ada data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Tabel waste --}}
        <div class="rs-card p-5">
            <h2 class="font-bold text-base mb-4" style="color: var(--rs-ink);">Waste Rate (dibuang / dibeli) — target &lt;10%</h2>
            <table class="datatable w-full text-sm">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Total Beli</th>
                        <th>Total Dibuang</th>
                        <th>Total Jual Murah</th>
                        <th>Waste Rate</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($ringkasan['waste_rate'] as $w)
                        @php $rate = $w->total_beli > 0 ? $w->total_dibuang / $w->total_beli * 100 : null; @endphp
                        <tr>
                            <td class="font-semibold">{{ $w->nama }}</td>
                            <td>{{ number_format($w->total_beli, 2) }}</td>
                            <td>{{ number_format($w->total_dibuang, 2) }}</td>
                            <td>{{ number_format($w->total_jual_murah, 2) }}</td>
                            <td>
                                @if ($rate !== null)
                                    <span class="rs-badge {{ $rate < 10 ? 'siap_diambil' : 'cancelled' }}">{{ number_format($rate, 2) }}%</span>
                                @else
                                    <span style="color: var(--rs-muted);">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="p-4 text-center" style="color: var(--rs-muted);">Belum ada data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
