<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BotSession extends Model
{
    protected $table = 'bot_sessions';

    protected $fillable = [
        'no_hp', 'nama', 'konsumen_id', 'state', 'data', 'percobaan', 'last_activity_at',
    ];

    protected $casts = [
        'data' => 'array',
        'last_activity_at' => 'datetime',
    ];

    public const STATE_IDLE = 'idle';
    public const STATE_NAMA = 'nama';
    public const STATE_PRODUK = 'produk';
    public const STATE_QTY = 'qty';
    public const STATE_TAMBAH = 'tambah';
    public const STATE_TGL_AMBIL = 'tgl_ambil';
    public const STATE_KONFIRMASI = 'konfirmasi';

    public const TIMEOUT_MENIT = 15;
    public const MAX_PERCOBAAN = 3;

    public function konsumen(): BelongsTo
    {
        return $this->belongsTo(Konsumen::class, 'konsumen_id');
    }

    public function keranjang(): array
    {
        return $this->data['keranjang'] ?? [];
    }

    public function produkAktifId(): ?int
    {
        return $this->data['produk_id'] ?? null;
    }

    public function sudahTimeout(): bool
    {
        return $this->last_activity_at
            && $this->last_activity_at->diffInMinutes(now()) > self::TIMEOUT_MENIT;
    }

    public function sentuh(string $state, array $data = []): void
    {
        $this->update([
            'state' => $state,
            'data' => array_merge($this->data ?? [], $data),
            'percobaan' => 0,
            'last_activity_at' => now(),
        ]);
    }

    public function reset(): void
    {
        $this->update([
            'state' => self::STATE_IDLE,
            'data' => null,
            'percobaan' => 0,
            'last_activity_at' => now(),
        ]);
    }
}
