<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class AkurasiSheet implements FromCollection, WithHeadings, WithTitle
{
    public function __construct(protected $rows) {}

    public function collection(): \Illuminate\Support\Enumerable
    {
        return $this->rows->map(fn ($r) => [
            'nama' => $r->nama,
            'n' => $r->n,
            'avg_mape' => $r->avg_mape !== null ? round((float) $r->avg_mape, 2) : null,
            'avg_mae' => $r->avg_mae !== null ? round((float) $r->avg_mae, 2) : null,
            'status' => $r->avg_mape !== null ? ($r->avg_mape < 20 ? 'Memenuhi' : 'Belum memenuhi') : '-',
        ]);
    }

    public function headings(): array
    {
        return ['Produk', 'N', 'Rata-rata MAPE (%)', 'Rata-rata MAE', 'Status Target (<20%)'];
    }

    public function title(): string
    {
        return 'Akurasi';
    }
}
