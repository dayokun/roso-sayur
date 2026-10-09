<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Produk extends Model
{
    protected $table = 'produk';

    protected $fillable = [
        'nama', 'satuan', 'harga_jual', 'kategori_id', 'is_seasonal', 'is_available',
    ];

    protected $casts = [
        'harga_jual' => 'decimal:2',
        'is_seasonal' => 'boolean',
        'is_available' => 'boolean',
    ];

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriProduk::class, 'kategori_id');
    }

    public function pesananDetails(): HasMany
    {
        return $this->hasMany(PesananDetail::class, 'produk_id');
    }
}
