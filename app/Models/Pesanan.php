<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Pesanan extends Model
{
    protected $table = 'pesanan';

    protected $fillable = [
        'kode', 'konsumen_id', 'tgl_pesan', 'tgl_ambil', 'input_source', 'status',
        'is_late_order', 'late_order_status', 'late_order_rejection_reason', 'cancel_reason', 'notified_at',
    ];

    protected $casts = [
        'tgl_pesan' => 'datetime',
        'tgl_ambil' => 'date',
        'is_late_order' => 'boolean',
        'notified_at' => 'datetime',
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_DITERIMA = 'diterima';
    public const STATUS_SIAP_DIAMBIL = 'siap_diambil';
    public const STATUS_SELESAI = 'selesai';
    public const STATUS_CANCELLED = 'cancelled';

    protected static function booted(): void
    {
        static::creating(function (Pesanan $pesanan) {
            if (empty($pesanan->kode)) {
                $pesanan->kode = self::buatKode($pesanan->tgl_pesan);
            }
        });
    }

    /**
     * Buat kode pesanan unik per hari, mis. RS-20261007-0003.
     * Nomor urut reset setiap hari agar pendek dan mudah disebut.
     */
    public static function buatKode($tglPesan = null): string
    {
        $prefix = 'RS-' . ($tglPesan ? Carbon::parse($tglPesan)->format('Ymd') : now()->format('Ymd')) . '-';
        $seq = static::where('kode', 'like', $prefix . '%')->count() + 1;
        $kode = $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);

        while (static::where('kode', $kode)->exists()) {
            $seq++;
            $kode = $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
        }

        return $kode;
    }

    public function konsumen(): BelongsTo
    {
        return $this->belongsTo(Konsumen::class, 'konsumen_id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(PesananDetail::class, 'pesanan_id');
    }
}
