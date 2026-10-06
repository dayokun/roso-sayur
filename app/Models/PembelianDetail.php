<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PembelianDetail extends Model
{
    protected $table = 'pembelian_detail';

    protected $fillable = [
        'pembelian_id', 'produk_id', 'prediksi_id',
        'is_available_today', 'qty_beli', 'harga_beli',
    ];

    protected $casts = [
        'is_available_today' => 'boolean',
        'qty_beli' => 'decimal:2',
        'harga_beli' => 'decimal:2',
    ];

    public function pembelian(): BelongsTo
    {
        return $this->belongsTo(Pembelian::class, 'pembelian_id');
    }

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }

    public function prediksi(): BelongsTo
    {
        return $this->belongsTo(Prediksi::class, 'prediksi_id');
    }

    public function sisaStok(): HasOne
    {
        return $this->hasOne(SisaStok::class, 'pembelian_detail_id');
    }
}
