<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DisposisiSisaStokRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status_sisa' => 'required|in:dijual_murah,dibuang',
            'harga_jual_murah' => 'nullable|numeric|min:0|required_if:status_sisa,dijual_murah',
            'qty_terjual_murah' => 'nullable|numeric|min:0',
            'qty_dibuang' => 'nullable|numeric|min:0',
        ];
    }
}
