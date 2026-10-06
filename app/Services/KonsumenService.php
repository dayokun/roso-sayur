<?php

namespace App\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\Konsumen;

/**
 * Daftar konsumen (terdaftar otomatis via bot WhatsApp).
 */
class KonsumenService
{
    public function daftar(): LengthAwarePaginator
    {
        return Konsumen::withCount('pesanans')
            ->orderBy('nama')
            ->paginate(20);
    }
}
