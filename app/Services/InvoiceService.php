<?php

namespace App\Services;

use App\Models\Pesanan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

/**
 * Generate invoice PDF untuk pesanan yang selesai.
 */
class InvoiceService
{
    /**
     * Buat PDF invoice dari pesanan selesai.
     *
     * @return array{path: string, url: string, total: float, filename: string}
     */
    public function buatPdf(Pesanan $pesanan): array
    {
        $pesanan->loadMissing(['details.produk', 'konsumen']);

        $items = [];
        $total = 0;

        foreach ($pesanan->details as $d) {
            $qty = (float) ($d->qty_delivered ?? $d->qty_pesan);
            $harga = (float) ($d->produk->harga_jual ?? 0);
            $subtotal = round($qty * $harga, 2);
            $total += $subtotal;

            $items[] = [
                'nama' => $d->produk->nama,
                'qty' => $qty,
                'satuan' => $d->satuan,
                'harga' => $harga,
                'subtotal' => $subtotal,
            ];
        }

        $total = round($total, 2);
        $kode = $pesanan->kode ?? 'RS-' . $pesanan->id;
        $filename = "invoice-{$kode}.pdf";

        $pdf = Pdf::loadView('invoice.pdf', [
            'pesanan' => $pesanan,
            'items' => $items,
            'total' => $total,
            'kode' => $kode,
        ]);

        $path = "invoices/{$filename}";
        Storage::disk('public')->put($path, $pdf->output());

        return [
            'path' => $path,
            'url' => asset("storage/{$path}"),
            'total' => $total,
            'filename' => $filename,
        ];
    }
}
