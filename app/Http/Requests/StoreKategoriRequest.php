<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreKategoriRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_kategori' => 'required|string|max:100|unique:kategori_produk,nama_kategori',
            'safety_buffer_persen' => 'required|numeric|min:0|max:15',
        ];
    }

    public function messages(): array
    {
        return [
            'safety_buffer_persen.max' => 'Safety buffer maksimal 15% (PRD).',
        ];
    }
}
