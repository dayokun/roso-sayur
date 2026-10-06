<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Konsumen extends Model
{
    protected $table = 'konsumen';

    protected $fillable = ['nama', 'no_hp', 'alamat'];

    public function pesanans(): HasMany
    {
        return $this->hasMany(Pesanan::class, 'konsumen_id');
    }
}
