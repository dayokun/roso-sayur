<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UnlockNotifikasiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdminIt() ?? false;
    }

    public function rules(): array
    {
        return [
            'tgl' => 'required|date',
            'alasan' => 'required|string|min:10',
        ];
    }
}
