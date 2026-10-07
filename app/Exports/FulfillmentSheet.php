<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class FulfillmentSheet implements FromCollection, WithHeadings, WithTitle
{
    public function __construct(protected $rows) {}

    public function collection(): \Illuminate\Support\Enumerable
    {
        return $this->rows->map(function ($f) {
            $rate = $f->total_pesan > 0 ? round($f->total_delivered / $f->total_pesan * 100, 1) : null;

            return [
                'nama' => $f->nama,
                'total_pesan' => round((float) $f->total_pesan, 2),
                'total_delivered' => round((float) $f->total_delivered, 2),
                'fulfillment' => $rate,
                'status' => $rate !== null ? ($rate > 95 ? 'Memenuhi' : 'Belum memenuhi') : '-',
            ];
        });
    }

    public function headings(): array
    {
        return ['Produk', 'Total Pesan', 'Total Delivered', 'Fulfillment (%)', 'Status Target (>95%)'];
    }

    public function title(): string
    {
        return 'Fulfillment';
    }
}
