<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Export;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class LaporanExport implements WithMultipleSheets, Export
{
    use Exportable;

    public function __construct(
        protected array $ringkasan,
        protected Carbon $dari,
        protected Carbon $sampai,
    ) {}

    public function sheets(): array
    {
        return [
            new AkurasiSheet($this->ringkasan['akurasi']),
            new FulfillmentSheet($this->ringkasan['fulfillment']),
            new WasteRateSheet($this->ringkasan['waste_rate']),
        ];
    }
}
