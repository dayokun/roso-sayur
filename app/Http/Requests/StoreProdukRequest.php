<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProdukRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:100|unique:produk,nama',
            'satuan' => 'required|string|max:20',
            'harga_jual' => 'nullable|numeric|min:0',
            'kategori_id' => 'required|exists:kategori_produk,id',
            'is_available' => 'nullable|boolean',
            'is_seasonal' => 'nullable|boolean',
        ];
    }
}
