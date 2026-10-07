<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Roso Sayur</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; }
        h1 { font-size: 18px; }
        h2 { font-size: 14px; margin-top: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #999; padding: 4px 6px; text-align: left; }
        th { background: #eee; }
        .ok { color: green; font-weight: bold; }
        .nok { color: red; font-weight: bold; }
    </style>
</head>
<body>
    <h1>Laporan Roso Sayur</h1>
    <p>Periode: {{ $dari }} s/d {{ $sampai }}</p>

    <h2>Akurasi Prediksi (MAPE / MAE)</h2>
    <table>
        <thead><tr><th>Produk</th><th>N</th><th>Rata-rata MAPE</th><th>Rata-rata MAE</th><th>Status</th></tr></thead>
        <tbody>
            @foreach ($ringkasan['akurasi'] as $r)
                <tr>
                    <td>{{ $r->nama }}</td>
                    <td>{{ $r->n }}</td>
                    <td>{{ $r->avg_mape !== null ? number_format($r->avg_mape, 2) . '%' : '-' }}</td>
                    <td>{{ $r->avg_mae !== null ? number_format($r->avg_mae, 2) : '-' }}</td>
                    <td class="{{ $r->avg_mape !== null && $r->avg_mape < 20 ? 'ok' : 'nok' }}">
                        {{ $r->avg_mape !== null ? ($r->avg_mape < 20 ? 'Memenuhi' : 'Belum memenuhi') : '-' }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Fulfillment Rate</h2>
    <table>
        <thead><tr><th>Produk</th><th>Total Pesan</th><th>Total Delivered</th><th>Fulfillment</th><th>Status</th></tr></thead>
        <tbody>
            @foreach ($ringkasan['fulfillment'] as $f)
                @php $rate = $f->total_pesan > 0 ? $f->total_delivered / $f->total_pesan * 100 : null; @endphp
                <tr>
                    <td>{{ $f->nama }}</td>
                    <td>{{ number_format($f->total_pesan, 2) }}</td>
                    <td>{{ number_format($f->total_delivered, 2) }}</td>
                    <td>{{ $rate !== null ? number_format($rate, 1) . '%' : '-' }}</td>
                    <td class="{{ $rate !== null && $rate > 95 ? 'ok' : 'nok' }}">
                        {{ $rate !== null ? ($rate > 95 ? 'Memenuhi' : 'Belum memenuhi') : '-' }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Waste Rate</h2>
    <table>
        <thead><tr><th>Produk</th><th>Total Beli</th><th>Total Dibuang</th><th>Waste Rate</th><th>Status</th></tr></thead>
        <tbody>
            @foreach ($ringkasan['waste_rate'] as $w)
                @php $rate = $w->total_beli > 0 ? $w->total_dibuang / $w->total_beli * 100 : null; @endphp
                <tr>
                    <td>{{ $w->nama }}</td>
                    <td>{{ number_format($w->total_beli, 2) }}</td>
                    <td>{{ number_format($w->total_dibuang, 2) }}</td>
                    <td>{{ $rate !== null ? number_format($rate, 2) . '%' : '-' }}</td>
                    <td class="{{ $rate !== null && $rate < 10 ? 'ok' : 'nok' }}">
                        {{ $rate !== null ? ($rate < 10 ? 'Memenuhi' : 'Belum memenuhi') : '-' }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
