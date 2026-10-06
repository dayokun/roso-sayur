<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PesananDetail extends Model
{
    protected $table = 'pesanan_detail';

    protected $fillable = [
        'pesanan_id', 'produk_id', 'qty_pesan', 'satuan',
        'qty_delivered', 'selisih', 'is_anomali',
    ];

    protected $casts = [
        'qty_pesan' => 'decimal:2',
        'qty_delivered' => 'decimal:2',
        'selisih' => 'decimal:2',
        'is_anomali' => 'boolean',
    ];

    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class, 'pesanan_id');
    }

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }
}
