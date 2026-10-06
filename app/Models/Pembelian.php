<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pembelian extends Model
{
    protected $table = 'pembelian';

    protected $fillable = ['tgl_beli', 'catatan'];

    protected $casts = ['tgl_beli' => 'date'];

    public function details(): HasMany
    {
        return $this->hasMany(PembelianDetail::class, 'pembelian_id');
    }
}
