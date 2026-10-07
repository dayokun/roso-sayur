<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Collection;
use App\Models\Konsumen;

/**
 * Daftar konsumen (terdaftar otomatis via bot WhatsApp).
 */
class KonsumenService
{
    public function daftar(): Collection
    {
        return Konsumen::withCount('pesanans')
            ->orderBy('nama')
            ->get();
    }
}
