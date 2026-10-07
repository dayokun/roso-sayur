<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BatalkanPesananRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'alasan' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'alasan.required' => 'Alasan pembatalan wajib diisi.',
        ];
    }
}
