<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $kode }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; margin: 0; padding: 24px; }
        .header { text-align: center; border-bottom: 2px solid #15803d; padding-bottom: 12px; margin-bottom: 16px; }
        .header h1 { color: #15803d; margin: 0; font-size: 22px; }
        .header p { margin: 4px 0 0; color: #666; }
        .info { margin-bottom: 16px; }
        .info table { width: 100%; }
        .info td { padding: 2px 0; vertical-align: top; }
        .label { color: #666; width: 130px; }
        table.items { width: 100%; border-collapse: collapse; margin-top: 8px; }
        table.items th { background: #15803d; color: #fff; padding: 8px; text-align: left; }
        table.items td { padding: 8px; border-bottom: 1px solid #ddd; }
        table.items tr:last-child td { border-bottom: none; }
        .num { text-align: right; }
        .total-row td { font-weight: bold; font-size: 14px; border-top: 2px solid #15803d; }
        .footer { margin-top: 24px; text-align: center; color: #666; font-size: 11px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>ROSO SAYUR</h1>
        <p>Sayur &amp; Buah Segar</p>
    </div>

    <div class="info">
        <table>
            <tr>
                <td class="label">No. Invoice</td>
                <td>: <strong>{{ $kode }}</strong></td>
                <td class="label">Tanggal</td>
                <td>: {{ \Carbon\Carbon::parse($pesanan->tgl_ambil)->format('d M Y') }}</td>
            </tr>
            <tr>
                <td class="label">Customer</td>
                <td>: {{ $pesanan->konsumen->nama ?? '-' }}</td>
                <td class="label">No. HP</td>
                <td>: {{ $pesanan->konsumen->no_hp ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <table class="items">
        <thead>
            <tr>
                <th>No</th>
                <th>Produk</th>
                <th class="num">Qty</th>
                <th class="num">Harga</th>
                <th class="num">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $i => $it)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $it['nama'] }}</td>
                    <td class="num">{{ number_format($it['qty'], 2) }} {{ $it['satuan'] }}</td>
                    <td class="num">Rp {{ number_format($it['harga'], 0, ',', '.') }}</td>
                    <td class="num">Rp {{ number_format($it['subtotal'], 0, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="4" class="num">TOTAL</td>
                <td class="num">Rp {{ number_format($total, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>Terima kasih telah berbelanja di Roso Sayur.</p>
        <p>Tunjukkan invoice ini saat pengambilan barang.</p>
    </div>
</body>
</html>
