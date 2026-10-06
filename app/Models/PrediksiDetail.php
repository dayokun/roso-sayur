<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrediksiDetail extends Model
{
    protected $table = 'prediksi_detail';

    protected $fillable = [
        'prediksi_id', 'rule_id', 'alpha_pesanan', 'alpha_historis',
        'alpha_tren', 'alpha_value', 'output_term', 'z_value', 'bobot',
    ];

    protected $casts = [
        'alpha_pesanan' => 'decimal:4',
        'alpha_historis' => 'decimal:4',
        'alpha_tren' => 'decimal:4',
        'alpha_value' => 'decimal:4',
        'z_value' => 'decimal:4',
        'bobot' => 'decimal:4',
    ];

    public function prediksi(): BelongsTo
    {
        return $this->belongsTo(Prediksi::class, 'prediksi_id');
    }
}
