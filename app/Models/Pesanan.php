<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pesanan extends Model
{
    protected $table = 'pesanan';

    protected $fillable = [
        'konsumen_id', 'tgl_pesan', 'tgl_ambil', 'input_source', 'status',
        'is_late_order', 'late_order_status', 'late_order_rejection_reason', 'notified_at',
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

    public function konsumen(): BelongsTo
    {
        return $this->belongsTo(Konsumen::class, 'konsumen_id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(PesananDetail::class, 'pesanan_id');
    }
}
