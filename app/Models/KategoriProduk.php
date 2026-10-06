<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KategoriProduk extends Model
{
    protected $table = 'kategori_produk';

    protected $fillable = ['nama_kategori', 'safety_buffer_persen'];

    protected $casts = [
        'safety_buffer_persen' => 'decimal:2',
    ];

    public function produks(): HasMany
    {
        return $this->hasMany(Produk::class, 'kategori_id');
    }
}
