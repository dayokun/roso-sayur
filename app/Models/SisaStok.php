<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SisaStok extends Model
{
    protected $table = 'sisa_stok';

    protected $fillable = [
        'pembelian_detail_id', 'tgl', 'qty_sisa', 'status_sisa',
        'harga_jual_murah', 'qty_terjual_murah', 'qty_dibuang',
    ];

    protected $casts = [
        'tgl' => 'date',
        'qty_sisa' => 'decimal:2',
        'harga_jual_murah' => 'decimal:2',
        'qty_terjual_murah' => 'decimal:2',
        'qty_dibuang' => 'decimal:2',
    ];

    public const STATUS_JUAL_MURAH = 'dijual_murah';
    public const STATUS_DIBUANG = 'dibuang';

    public function pembelianDetail(): BelongsTo
    {
        return $this->belongsTo(PembelianDetail::class, 'pembelian_detail_id');
    }
}
