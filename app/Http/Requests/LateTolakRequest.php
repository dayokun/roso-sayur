<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LateTolakRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'alasan' => 'required|in:stok_habis,terlambat_pesan',
        ];
    }
}
