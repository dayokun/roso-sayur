<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProdukMfConfig extends Model
{
    protected $table = 'produk_mf_config';

    protected $fillable = [
        'produk_id', 'variabel', 'batas_bawah', 'batas_tengah', 'batas_atas',
        'generated_from', 'valid_from', 'valid_until', 'generated_at',
    ];

    protected $casts = [
        'batas_bawah' => 'decimal:2',
        'batas_tengah' => 'decimal:2',
        'batas_atas' => 'decimal:2',
        'valid_from' => 'date',
        'valid_until' => 'date',
        'generated_at' => 'datetime',
    ];

    public const VARIABEL_PESANAN = 'pesanan';
    public const VARIABEL_HISTORIS = 'historis';

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }

    public function prediksis(): HasMany
    {
        return $this->hasMany(Prediksi::class, 'mf_config_version_id');
    }

    /**
     * Ambil konfigurasi MF yang aktif untuk produk + variabel pada tanggal tertentu.
     */
    public static function aktif(int $produkId, string $variabel, $tanggal = null): ?self
    {
        $tanggal = $tanggal ?: now()->toDateString();

        return self::where('produk_id', $produkId)
            ->where('variabel', $variabel)
            ->where('valid_from', '<=', $tanggal)
            ->where(function ($q) use ($tanggal) {
                $q->whereNull('valid_until')->orWhere('valid_until', '>=', $tanggal);
            })
            ->latest('valid_from')
            ->first();
    }
}
