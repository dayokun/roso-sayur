<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Prediksi extends Model
{
    protected $table = 'prediksi';

    protected $fillable = [
        'produk_id', 'tgl_prediksi', 'qty_rekomendasi', 'qty_dengan_buffer',
        'input_pesanan', 'input_historis', 'input_tren', 'mf_config_version_id',
    ];

    protected $casts = [
        'tgl_prediksi' => 'date',
        'qty_rekomendasi' => 'decimal:2',
        'qty_dengan_buffer' => 'decimal:2',
        'input_pesanan' => 'decimal:2',
        'input_historis' => 'decimal:2',
        'input_tren' => 'decimal:4',
    ];

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }

    public function mfConfig(): BelongsTo
    {
        return $this->belongsTo(ProdukMfConfig::class, 'mf_config_version_id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(PrediksiDetail::class, 'prediksi_id');
    }
}
