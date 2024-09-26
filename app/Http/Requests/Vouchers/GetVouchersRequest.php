<?php

namespace App\Http\Requests\Vouchers;

use Illuminate\Foundation\Http\FormRequest;

class GetVouchersRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'page' => ['required', 'int', 'gt:0'],
            'paginate' => ['required', 'int', 'gt:0'],
            'serie' => ['sometimes', 'nullable'],
            'number' => ['sometimes', 'nullable'],
            'date_start' => ['sometimes', 'nullable', 'date'],
            'date_end' => ['sometimes', 'nullable', 'date'],
        ];
    }
}
