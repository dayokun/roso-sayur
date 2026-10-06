<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotifikasiUnlockLog extends Model
{
    protected $table = 'notifikasi_unlock_log';

    protected $fillable = ['admin_id', 'tgl', 'alasan', 'timestamp_unlock'];

    protected $casts = [
        'tgl' => 'date',
        'timestamp_unlock' => 'datetime',
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
