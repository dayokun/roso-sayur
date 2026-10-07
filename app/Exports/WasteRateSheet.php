<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class WasteRateSheet implements FromCollection, WithHeadings, WithTitle
{
    public function __construct(protected $rows) {}

    public function collection(): \Illuminate\Support\Enumerable
    {
        return $this->rows->map(function ($w) {
            $rate = $w->total_beli > 0 ? round($w->total_dibuang / $w->total_beli * 100, 2) : null;

            return [
                'nama' => $w->nama,
                'total_beli' => round((float) $w->total_beli, 2),
                'total_dibuang' => round((float) $w->total_dibuang, 2),
                'total_jual_murah' => round((float) $w->total_jual_murah, 2),
                'waste_rate' => $rate,
                'status' => $rate !== null ? ($rate < 10 ? 'Memenuhi' : 'Belum memenuhi') : '-',
            ];
        });
    }

    public function headings(): array
    {
        return ['Produk', 'Total Beli', 'Total Dibuang', 'Total Jual Murah', 'Waste Rate (%)', 'Status Target (<10%)'];
    }

    public function title(): string
    {
        return 'Waste Rate';
    }
}
