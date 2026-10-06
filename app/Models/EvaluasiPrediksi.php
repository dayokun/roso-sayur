<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvaluasiPrediksi extends Model
{
    protected $table = 'evaluasi_prediksi';

    protected $fillable = ['prediksi_id', 'qty_aktual_demand', 'mape', 'mae'];

    protected $casts = [
        'qty_aktual_demand' => 'decimal:2',
        'mape' => 'decimal:4',
        'mae' => 'decimal:4',
    ];

    public function prediksi(): BelongsTo
    {
        return $this->belongsTo(Prediksi::class, 'prediksi_id');
    }
}
