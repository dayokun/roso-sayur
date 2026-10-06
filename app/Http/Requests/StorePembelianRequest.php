<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePembelianRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tgl' => 'required|date',
            'items' => 'required|array',
            'items.*.produk_id' => 'required|exists:produk,id',
            'items.*.qty_beli' => 'nullable|numeric|min:0',
            'items.*.harga_beli' => 'nullable|numeric|min:0',
            'items.*.is_available_today' => 'nullable|boolean',
            'catatan' => 'nullable|string',
        ];
    }
}
